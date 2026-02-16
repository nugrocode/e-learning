<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use App\Services\GeminiService;
use App\Models\Course;
use App\Models\Material;
use App\Models\Progress;
use App\Models\Submission;
use App\Models\QuizScore;
use App\Models\QuizQuestion;
use App\Models\Discussion;
use App\Models\User;
use App\Models\Notification;

class DosenController extends Controller
{
    protected $gemini;

    public function __construct(GeminiService $geminiService)
    {
        $this->gemini = $geminiService;
    }

    private function convertToEmbedUrl($url)
    {
        if (empty($url)) return null;
        $pattern = '%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i';
        if (preg_match($pattern, $url, $match)) {
            return 'https://www.youtube.com/embed/' . $match[1];
        }
        return $url;
    }

    private function reorderMaterials($course_id)
    {
        $mats = Material::where('course_id', $course_id)->where('urutan', '>', 0)->orderBy('urutan', 'asc')->get();
        foreach($mats as $idx => $m) {
            $m->update(['urutan' => $idx + 1]);
        }
    }

    public function dashboard()
    {
        $id = Session::get('user_id');
        $total_kelas = Course::where('dosen_id', $id)->count();
        $kelas_list = Course::withCount('materials')->where('dosen_id', $id)->get();
        $total_mhs = Progress::join('materials', 'progress.material_id', '=', 'materials.id')
            ->join('courses', 'materials.course_id', '=', 'courses.id')
            ->where('courses.dosen_id', $id)->distinct('progress.user_id')->count('progress.user_id');
        $total_tugas = Submission::join('materials', 'submissions.material_id', '=', 'materials.id')
            ->join('courses', 'materials.course_id', '=', 'courses.id')
            ->where('courses.dosen_id', $id)->count();
        return view('dosen.dashboard', compact('total_kelas', 'total_mhs', 'total_tugas', 'kelas_list'));
    }

    public function mahasiswaIndex(Request $request)
    {
        $dosen_id = Session::get('user_id');
        $courses = Course::where('dosen_id', $dosen_id)->get();
        $query = User::where('role', 'mahasiswa')
            ->whereHas('progress.material.course', function($q) use ($dosen_id) {
                $q->where('dosen_id', $dosen_id);
            });
        if ($request->filled('kelas_id')) {
            $query->whereHas('progress.material', function($q) use ($request) {
                $q->where('course_id', $request->kelas_id);
            });
        }
        if ($request->filled('q')) {
            $query->where('nama_lengkap', 'like', '%'.$request->q.'%');
        }
        $students = $query->paginate(10)->withQueryString();
        $students->getCollection()->transform(function ($student) use ($dosen_id) {
            $student->courses_taken = Course::where('dosen_id', $dosen_id)
                ->whereHas('materials.progress', function($q) use ($student) {
                    $q->where('user_id', $student->id);
                })->get();
            return $student;
        });
        return view('dosen.mahasiswa', compact('students', 'courses'));
    }

    public function mahasiswaKick(Request $request)
    {
        $material_ids = Material::where('course_id', $request->course_id)->pluck('id');
        Progress::where('user_id', $request->user_id)->whereIn('material_id', $material_ids)->delete();
        Submission::where('user_id', $request->user_id)->whereIn('material_id', $material_ids)->delete();
        QuizScore::where('user_id', $request->user_id)->whereIn('material_id', $material_ids)->delete();
        return back()->with('success', 'Data progress mahasiswa berhasil di-reset.');
    }

    public function materiIndex(Request $request)
    {
        $courses = Course::where('dosen_id', Session::get('user_id'))->withCount('materials')->get();
        return view('dosen.materi', compact('courses'));
    }

    public function materiShow($id)
    {
        $course = Course::where('id', $id)->where('dosen_id', Session::get('user_id'))->firstOrFail();
        $materials = Material::where('course_id', $id)->where('urutan', '>', 0)->orderBy('urutan', 'asc')->get();
        $pending_materials = Material::where('course_id', $id)->where('urutan', 0)->get();
        return view('dosen.susun_materi', compact('course', 'materials', 'pending_materials'));
    }

    public function materiStore(Request $request)
    {
        $request->validate(['judul_materi' => 'required', 'course_id' => 'required']);
        $urutan = 0;
        if ($request->kategori == 'quiz') {
            if ($request->filled('insert_after')) {
                if ($request->insert_after == 'start') {
                    $urutan = 1;
                } else {
                    $prev = Material::find($request->insert_after);
                    $urutan = $prev ? $prev->urutan + 1 : 1;
                }
                Material::where('course_id', $request->course_id)->where('urutan', '>=', $urutan)->increment('urutan');
            } else {
                $last = Material::where('course_id', $request->course_id)->max('urutan');
                $urutan = $last + 1;
            }
        }
        $filename = null;
        if ($request->kategori == 'video' && $request->hasFile('file_lampiran')) {
            $filename = time() . '_' . $request->file('file_lampiran')->getClientOriginalName();
            $request->file('file_lampiran')->storeAs('materials', $filename, 'public');
        }
        $videoUrl = $this->convertToEmbedUrl($request->video_url);
        $tipeSubmission = ($request->tipe_submission == 'none') ? null : $request->tipe_submission;
        $linkDrive = $request->link_drive;
        if ($request->kategori == 'quiz') {
            $videoUrl = null;
            $tipeSubmission = null;
            $linkDrive = null;
            $filename = null;
        } elseif ($tipeSubmission != 'file') {
            $linkDrive = null;
        }
        Material::create([
            'course_id' => $request->course_id,
            'judul_materi' => $request->judul_materi,
            'deskripsi_materi' => $request->deskripsi_materi,
            'kategori' => $request->kategori,
            'tipe_submission' => $tipeSubmission,
            'video_url' => $videoUrl,
            'link_drive' => $linkDrive,
            'file_lampiran' => $filename,
            'urutan' => $urutan
        ]);
        return back()->with('success', $request->kategori == 'quiz' ? 'Kuis berhasil disisipkan.' : 'Materi Video berhasil masuk antrean AI.');
    }

    public function materiUpdate(Request $request, $id)
    {
        $m = Material::findOrFail($id);
        if ($m->kategori == 'video' && $request->hasFile('file_lampiran')) {
            if ($m->file_lampiran) Storage::disk('public')->delete('materials/' . $m->file_lampiran);
            $filename = time() . '_' . $request->file('file_lampiran')->getClientOriginalName();
            $request->file('file_lampiran')->storeAs('materials', $filename, 'public');
            $m->file_lampiran = $filename;
            $m->save();
        }
        $data = $request->except(['file_lampiran', 'token', 'kategori']);
        if (isset($data['tipe_submission']) && $data['tipe_submission'] == 'none') {
            $data['tipe_submission'] = null;
        }
        if ($m->kategori == 'quiz') {
            $data['video_url'] = null;
            $data['tipe_submission'] = null;
            $data['link_drive'] = null;
        } else {
            if (!empty($data['video_url'])) {
                $data['video_url'] = $this->convertToEmbedUrl($data['video_url']);
            }
            if (isset($data['tipe_submission']) && $data['tipe_submission'] != 'file') {
                $data['link_drive'] = null;
            }
        }
        $m->update($data);
        return back()->with('success', 'Materi berhasil diperbarui.');
    }

    public function materiDestroy($id)
    {
        $m = Material::findOrFail($id);
        $course_id = $m->course_id;
        if ($m->file_lampiran) Storage::disk('public')->delete('materials/' . $m->file_lampiran);
        QuizQuestion::where('material_id', $id)->delete();
        Progress::where('material_id', $id)->delete();
        Submission::where('material_id', $id)->delete();
        Discussion::where('material_id', $id)->delete();
        $m->delete();
        $this->reorderMaterials($course_id);
        return back()->with('success', 'Materi berhasil dihapus.');
    }

    public function aiSmartInsert($course_id)
    {
        $existing = Material::where('course_id', $course_id)->where('urutan', '>', 0)->orderBy('urutan', 'asc')->get();
        $newMats = Material::where('course_id', $course_id)->where('urutan', 0)->get();
        if ($newMats->isEmpty()) return back()->with('error', 'Tidak ada materi baru di antrean.');
        $existingData = $existing->map(function($m) { return "ID:{$m->id} | Judul: {$m->judul_materi}"; })->implode("\n");
        foreach ($newMats as $nm) {
            $prompt = "Anda adalah Ahli Kurikulum. Tugas Anda menyisipkan materi baru ke dalam silabus yang sudah ada. ATURAN PENTING: 1. 'Pengenalan', 'Dasar', 'Intro', 'Konsep' HARUS di awal (sebelum Lanjutan). 2. 'Lanjutan', 'Advanced', 'Implementasi' HARUS setelah materi Dasar. 3. 'Evaluasi', 'Kuis', 'Ujian' diletakkan SETELAH materi yang diujikan. 4. Jika materi baru adalah 'Dasar' dan belum ada materi dasar lain, letakkan paling awal (insert_after_id: 0). SILABUS SAAT INI (Berurut): {$existingData} MATERI BARU YANG AKAN DISISIPKAN: Judul: '{$nm->judul_materi}' Deskripsi: '{$nm->deskripsi_materi}' PERTANYAAN: Setelah ID berapakah materi baru ini harus diletakkan agar urutannya logis? Jawab HANYA JSON: {'insert_after_id': ID_YANG_DIPILIH} (Gunakan 0 jika harus paling pertama).";
            $result = $this->gemini->ask($prompt, true);
            $targetId = $result['insert_after_id'] ?? 0;
            $targetUrutan = 0;
            if ($targetId != 0) {
                $ref = Material::find($targetId);
                if ($ref && $ref->course_id == $course_id) {
                    $targetUrutan = $ref->urutan;
                }
            }
            Material::where('course_id', $course_id)->where('urutan', '>', $targetUrutan)->increment('urutan');
            $nm->update(['urutan' => $targetUrutan + 1]);
            $existing = Material::where('course_id', $course_id)->where('urutan', '>', 0)->orderBy('urutan', 'asc')->get();
            $existingData = $existing->map(function($m) { return "ID:{$m->id} | Judul: {$m->judul_materi}"; })->implode("\n");
        }
        $this->reorderMaterials($course_id);
        return back()->with('success', 'AI berhasil menyisipkan materi dengan logika kurikulum.');
    }

    public function aiAutoSort($course_id)
    {
        $materials = Material::where('course_id', $course_id)->get();
        if ($materials->isEmpty()) return back()->with('error', 'Materi kosong.');
        $data = $materials->map(function($m) { return "ID:{$m->id} | Judul:{$m->judul_materi} | Tipe:{$m->kategori}"; })->implode("\n");
        $prompt = "Anda adalah Arsitek Kurikulum Pembelajaran. Urutkan ulang materi-materi berikut agar membentuk alur belajar yang logis dari NOL sampai MAHIR. ATURAN PENGURUTAN (WAJIB PATUH): 1. LEVEL 1 (AWAL): Materi 'Pengenalan', 'Definisi', 'Konsep Dasar', 'Instalasi', 'Persiapan'. 2. LEVEL 2 (TENGAH): Materi 'Implementasi', 'Praktek', 'Studi Kasus', 'Fitur Utama'. 3. LEVEL 3 (LANJUT): Materi 'Lanjutan', 'Advanced', 'Optimasi', 'Security'. 4. POSISI KUIS: Jika ada materi bertipe 'KUIS' atau judul 'Evaluasi/Ujian', letakkan tepat SETELAH materi yang relevan (Jangan ditaruh di awal jika itu ujian akhir). 5. Jika ada 'Pengenalan Algoritma' dan 'Algoritma Lanjutan', pastikan Pengenalan DULUAN. DATA MATERI ACAK: {$data} OUTPUT: HANYA JSON Array berisi ID dalam urutan yang benar. Contoh: [5, 2, 1, 3]. Jangan ada teks lain.";
        $sortedIDs = $this->gemini->ask($prompt, true);
        if (is_array($sortedIDs) && count($sortedIDs) > 0) {
            foreach ($sortedIDs as $idx => $id) {
                Material::where('id', $id)->update(['urutan' => $idx + 1]);
            }
            return back()->with('success', 'Materi disusun ulang: Dasar -> Lanjut.');
        }
        return back()->with('error', 'AI gagal memberikan respon format JSON.');
    }

    public function kuisIndex(Request $request)
    {
        $dosen_id = Session::get('user_id');
        $courses = Course::where('dosen_id', $dosen_id)->get();
        $query = Material::where('kategori', 'quiz')->whereIn('course_id', $courses->pluck('id'))->withCount('questions');
        if ($request->course_id) $query->where('course_id', $request->course_id);
        $quizzes = $query->orderBy('created_at', 'desc')->get();
        return view('dosen.kuis', compact('quizzes', 'courses'));
    }

    public function soalStore(Request $request)
    {
        QuizQuestion::create($request->all());
        return back()->with('success', 'Butir soal berhasil ditambahkan.');
    }

    public function soalDestroy($id)
    {
        QuizQuestion::findOrFail($id)->delete();
        return back()->with('success', 'Soal dihapus.');
    }

    public function tugasIndex(Request $request)
    {
        $dosen_id = Session::get('user_id');
        $courses = Course::where('dosen_id', $dosen_id)->get();
        $query = Material::whereNotNull('tipe_submission')->whereIn('course_id', $courses->pluck('id'))->with(['submissions.user', 'course']);
        if ($request->course_id) $query->where('course_id', $request->course_id);
        if ($request->tipe) $query->where('tipe_submission', $request->tipe);
        $assignments = $query->orderBy('created_at', 'desc')->get();
        $all_sub_ids = Submission::whereIn('material_id', $assignments->pluck('id'))->get();
        $total_submissions = $all_sub_ids->count();
        $pending_grading = $all_sub_ids->whereNull('nilai')->count();
        return view('dosen.tugas', compact('assignments', 'courses', 'total_submissions', 'pending_grading'));
    }

    public function nilaiUpdate(Request $request, $id)
    {
        $sub = Submission::findOrFail($id);
        $sub->nilai = $request->nilai;
        $sub->save();
        Notification::create([
            'user_id' => $sub->user_id,
            'sender_id' => Session::get('user_id'),
            'material_id' => $sub->material_id,
            'course_id' => $sub->material->course_id,
            'message' => "Tugas Anda pada materi '{$sub->material->judul_materi}' telah dinilai: {$request->nilai}",
            'is_read' => 0
        ]);
        return back()->with('success', 'Nilai berhasil disimpan.');
    }

    public function diskusiIndex(Request $request)
    {
        $dosen_id = Session::get('user_id');
        $courses = Course::where('dosen_id', $dosen_id)->get();
        $query = Discussion::whereHas('material', function($q) use ($courses) {
            $q->whereIn('course_id', $courses->pluck('id'));
        })->whereNull('parent_id')->with(['user', 'material', 'replies']);
        if ($request->course_id) {
            $query->whereHas('material', function($q) use ($request) {
                $q->where('course_id', $request->course_id);
            });
        }
        if ($request->q) {
            $query->where('message', 'like', '%'.$request->q.'%');
        }
        $discussions = $query->orderBy('created_at', 'desc')->get();
        return view('dosen.diskusi', compact('discussions', 'courses'));
    }

    public function diskusiStore(Request $request)
    {
        Discussion::create([
            'course_id' => $request->course_id ?? Material::find($request->material_id)->course_id,
            'material_id' => $request->material_id,
            'user_id' => Session::get('user_id'),
            'message' => $request->message,
            'parent_id' => $request->parent_id
        ]);
        if ($request->parent_id) {
            $parent = Discussion::find($request->parent_id);
            if ($parent && $parent->user_id != Session::get('user_id')) {
                Notification::create([
                    'user_id' => $parent->user_id,
                    'sender_id' => Session::get('user_id'),
                    'material_id' => $request->material_id,
                    'course_id' => $request->course_id,
                    'message' => "Dosen membalas pertanyaan Anda.",
                    'is_read' => 0
                ]);
            }
        }
        return redirect(url()->previous() . '#content-diskusi')->with('success', 'Balasan terkirim.');
    }

    public function diskusiDestroy($id)
    {
        $chat = Discussion::findOrFail($id);
        $chat->replies()->delete();
        $chat->delete();
        return redirect(url()->previous() . '#content-diskusi')->with('success', 'Komentar dihapus.');
    }

    public function profilIndex()
    {
        $user = User::findOrFail(Session::get('user_id'));
        return view('dosen.profil', compact('user'));
    }

    public function profilUpdate(Request $request)
    {
        $user = User::findOrFail(Session::get('user_id'));
        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'file_foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'password_baru' => 'nullable|min:6'
        ]);
        if ($request->hasFile('file_foto')) {
            if ($user->foto_profil && $user->foto_profil != 'default.png') {
                Storage::disk('public')->delete('profiles/' . $user->foto_profil);
            }
            $file = $request->file('file_foto');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('profiles', $filename, 'public');
            $user->foto_profil = $filename;
            Session::put('foto', $filename);
        }
        if ($request->filled('password_baru')) {
            $user->password = Hash::make($request->password_baru);
        }
        $user->nama_lengkap = $request->nama_lengkap;
        $user->save();
        Session::put('nama', $user->nama_lengkap);
        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    public function preview($course_id, $urutan)
    {
        $materi = Material::where('course_id', $course_id)->where('urutan', $urutan)->firstOrFail();
        $daftar_materi = Material::where('course_id', $course_id)->orderBy('urutan')->get();
        return view('user.materi', [
            'materi' => $materi,
            'daftar_materi' => $daftar_materi,
            'urutan' => $urutan,
            'course_id' => $course_id,
            'data_tugas' => null,
            'data_nilai' => null,
            'soal_kuis' => $materi->kategori == 'quiz' ? QuizQuestion::where('material_id', $materi->id)->get() : [],
            'diskusi' => [],
            'is_preview' => true 
        ]);
    }
}