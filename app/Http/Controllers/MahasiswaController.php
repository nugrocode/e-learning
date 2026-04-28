<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Services\GeminiService;
use App\Helpers\DriveHelper;
use App\Models\Concentration;
use App\Models\Course;
use App\Models\Material;
use App\Models\Progress;
use App\Models\Submission;
use App\Models\QuizScore;
use App\Models\QuizQuestion;
use App\Models\Discussion;
use App\Models\User;
use App\Models\Notification;
use App\Models\Announcement;

class MahasiswaController extends Controller
{
    protected GeminiService $gemini;

    public function __construct(GeminiService $geminiService)
    {
        $this->gemini = $geminiService;
    }

    public function dashboard() 
    { 
        $announcements = Announcement::where('is_active', true)->latest()->get(); 
        return view('user.dashboard', compact('announcements')); 
    }
    
    public function index() 
    {
        $concentrations = Concentration::all();
        foreach($concentrations as $k) { 
            $k->total_mk = $k->courses()->count(); 
        }
        return view('user.jalur_belajar', compact('concentrations'));
    }

    public function myClasses() 
    {
        $user_id = Session::get('user_id');
        $courses = Course::whereHas('materials.progress', function($q) use ($user_id) { 
            $q->where('user_id', $user_id); 
        })->get();

        foreach ($courses as $course) {
            $materials = Material::where('course_id', $course->id)->orderBy('urutan', 'asc')->get();
            $total_materi = $materials->count();
            $selesai_count = 0;
            $next_urutan = 1;
            $found_next = false;

            foreach ($materials as $m) {
                $is_done = Progress::where('user_id', $user_id)->where('material_id', $m->id)->exists();
                if ($is_done) $selesai_count++;
                else if (!$found_next) { $next_urutan = $m->urutan; $found_next = true; }
            }
            
            $course->persen = $total_materi > 0 ? round(($selesai_count / $total_materi) * 100) : 0;
            $course->next_urutan = $next_urutan;
        }
        return view('user.kelas_saya', compact('courses'));
    }

    public function showCourses(string $id) 
    {
        Session::put('active_concentration_id', $id);
        $concentration = Concentration::findOrFail($id);
        
        $courses = $concentration->courses()
                        ->with('dosen')
                        ->wherePivot('urutan', '>', 0)
                        ->orderBy('concentration_course.urutan', 'asc') 
                        ->get();
                        
        $user_id = Session::get('user_id');
        $is_previous_completed = true;

        foreach ($courses as $course) {
            $materials = Material::where('course_id', $course->id)->orderBy('urutan', 'asc')->get();
            $total_materi = $materials->count();
            $selesai_count = 0;
            $next_urutan = 1;
            $found_next = false;

            foreach ($materials as $m) {
                $is_done = Progress::where('user_id', $user_id)->where('material_id', $m->id)->exists();
                if ($is_done) $selesai_count++;
                else if (!$found_next) { $next_urutan = $m->urutan; $found_next = true; }
            }

            $course->persen = $total_materi > 0 ? round(($selesai_count / $total_materi) * 100) : 0;
            $course->next_urutan = $next_urutan;
            $course->total_materi = $total_materi;

            if ($total_materi == 0) {
                $course->status_akses = 'empty'; 
                $course->pesan_kunci = 'Materi belum diinput oleh Dosen.';
            } elseif (!$is_previous_completed) {
                $course->status_akses = 'locked';
                $course->pesan_kunci = 'Selesaikan mata kuliah sebelumnya.';
            } else {
                $course->status_akses = 'open';
            }

            $is_previous_completed = ($total_materi > 0 && $course->persen == 100);
        }

        return view('user.mata_kuliah', compact('concentration', 'courses'));
    }

    public function belajar(string $course_id, int $urutan = 1) 
    {
        $user_id = Session::get('user_id');
        $materi = Material::with('course')->where('course_id', $course_id)->where('urutan', $urutan)->firstOrFail();
        
        if ($urutan > 1) {
            $prev = Material::where('course_id', $course_id)->where('urutan', $urutan - 1)->first();
            if ($prev && !Progress::where('user_id', $user_id)->where('material_id', $prev->id)->exists()) {
                return redirect()->back()->with('error', 'Selesaikan materi sebelumnya dulu!');
            }
        }

        $daftar_materi = Material::where('course_id', $course_id)->orderBy('urutan', 'asc')->get();
        $data_tugas = ($materi->kategori != 'quiz') ? Submission::where('user_id', $user_id)->where('material_id', $materi->id)->first() : null;
        
        $data_nilai = null;
        $soal_kuis = [];
        
        if ($materi->kategori == 'quiz') {
            $data_nilai = QuizScore::where('user_id', $user_id)->where('material_id', $materi->id)->first();
            if (!$data_nilai || request('mode') == 'retake') {
                $soal_kuis = QuizQuestion::where('material_id', $materi->id)->inRandomOrder()->limit(5)->get();
            }
        }
        
        $diskusi = Discussion::with(['user', 'replies.user'])
                    ->where('material_id', $materi->id)
                    ->whereNull('parent_id')
                    ->orderBy('created_at', 'desc')
                    ->get();
        
        return view('user.materi', compact('materi', 'daftar_materi', 'course_id', 'urutan', 'data_tugas', 'data_nilai', 'soal_kuis', 'diskusi'));
    }

    public function storeProgress(Request $request) 
    {
        $user_id = Session::get('user_id');
        Progress::firstOrCreate(['user_id' => $user_id, 'material_id' => $request->material_id], ['status' => 'selesai', 'tanggal_selesai' => now()]);
        
        $next = $request->urutan + 1;
        if (Material::where('course_id', $request->course_id)->where('urutan', $next)->exists()) {
            return redirect('/belajar/' . $request->course_id . '/' . $next);
        }
        return redirect('/mata-kuliah/' . Session::get('active_concentration_id'))->with('success', 'Kelas Selesai!');
    }

    public function storeQuiz(Request $request) 
    {
        $jawaban_user = $request->input('jawaban', []);
        if (empty($jawaban_user)) return back()->with('error', 'Anda belum menjawab satupun soal!');
        
        $jumlah_soal = count($jawaban_user);
        $jawaban_benar = 0;
        foreach ($jawaban_user as $soal_id => $jawaban_dipilih) {
            $kunci = QuizQuestion::find($soal_id);
            if ($kunci && $kunci->jawaban_benar == $jawaban_dipilih) $jawaban_benar++;
        }
        
        $skor_akhir = round(($jawaban_benar / $jumlah_soal) * 100);
        QuizScore::updateOrCreate(['user_id' => Session::get('user_id'), 'material_id' => $request->material_id], ['skor' => $skor_akhir, 'tanggal_kerja' => now()]);
        
        if ($skor_akhir >= 60) {
            Progress::firstOrCreate(['user_id' => Session::get('user_id'), 'material_id' => $request->material_id], ['status' => 'selesai', 'tanggal_selesai' => now()]);
        }
        return redirect('/belajar/' . $request->course_id . '/' . $request->urutan)->with('success', 'Kuis Selesai! Skor: ' . $skor_akhir);
    }

    public function storeAssignment(Request $request) 
    {
        $user_id = Session::get('user_id');
        $materi = Material::with('course.dosen')->findOrFail($request->material_id);
        $filePath = null;

        if ($materi->tipe_submission == 'file') {
            if (!$request->hasFile('file_tugas')) {
                return back()->with('error', 'Wajib upload file dokumen/tugas!');
            }

            $file = $request->file('file_tugas');
            $dosen = $materi->course->dosen;

            if ($dosen && $dosen->google_token) {
                try {
                    $folderId = null;
                    if (!empty($materi->link_drive)) {
                        $parsedUrl = parse_url($materi->link_drive);
                        $folderId = isset($parsedUrl['path']) ? basename($parsedUrl['path']) : basename($materi->link_drive);
                    }
                    $linkDrive = DriveHelper::uploadToDosenDrive($file, $dosen, $folderId);
                    $filePath = $linkDrive ?: $this->saveLocal($file, $user_id);
                } catch (\Exception $e) {
                    $filePath = $this->saveLocal($file, $user_id);
                }
            } else {
                $filePath = $this->saveLocal($file, $user_id);
            }
        } else {
            $filePath = $request->input('link_tugas') ?? $request->input('link_github');
            if (empty($filePath)) return back()->with('error', 'Link tugas wajib diisi!');
        }

        Submission::create([
            'user_id' => $user_id, 
            'material_id' => $request->material_id, 
            'file_path' => $filePath, 
            'nilai' => null
        ]);

        Progress::firstOrCreate(['user_id' => $user_id, 'material_id' => $request->material_id], ['status' => 'selesai', 'tanggal_selesai' => now()]);

        return back()->with('success', 'Tugas berhasil dikirim!');
    }

    private function saveLocal(\Illuminate\Http\UploadedFile $file, string $user_id) {
        $filename = time() . '_' . $user_id . '_' . $file->getClientOriginalName();
        $file->storeAs('submissions', $filename, 'public');
        return $filename;
    }

    public function askAi(Request $request) 
    {
        $pesan = $request->input('message'); 
        $prompt = "Role: Kamu adalah 'Asisten Pintar' mahasiswa IT. User bertanya: \"$pesan\". Jawab dengan singkat.";
        $reply = $this->gemini->ask($prompt, false); 
        return response()->json(['reply' => $reply ?? "Maaf, AI sedang gangguan."]);
    }

    public function editProfile() 
    { 
        $user = User::find(Session::get('user_id')); 
        return view('user.profil', compact('user')); 
    }
    
    public function updateProfile(Request $request) 
    { 
        $user = User::find(Session::get('user_id')); 
        $user->nama_lengkap = $request->nama_lengkap; 
        Session::put('nama', $request->nama_lengkap); 
        
        if($request->hasFile('foto')) { 
            if ($user->foto_profil && $user->foto_profil != 'default.png') {
                Storage::disk('public')->delete('profiles/' . $user->foto_profil);
            }
            $file = $request->file('foto'); 
            $filename = time().'_'.$file->getClientOriginalName(); 
            $file->storeAs('profiles', $filename, 'public'); 
            $user->foto_profil = $filename; 
            Session::put('foto', $filename); 
        } 
        
        if($request->filled('password')) $user->password = md5($request->password); 
        $user->save(); 
        return back()->with('success', 'Profil diperbarui!'); 
    }

    public function storeDiscussion(Request $request) 
    { 
        $diskusi = Discussion::create([
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
                    'message' => "Ada balasan baru di komentar Anda.", 
                    'is_read' => 0
                ]); 
            } 
        } 

        $diskusi->load('user');

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'data' => [
                    'id' => $diskusi->id,
                    'nama' => $diskusi->user->nama_lengkap,
                    'role' => $diskusi->user->role,
                    'foto' => $diskusi->user->foto_profil,
                    'isi' => $diskusi->message
                ]
            ]);
        }

        return back()->with('success', 'Komentar terkirim.'); 
    }

    public function destroyDiscussion(string $id) 
    { 
        $d = Discussion::find($id); 
        if ($d && $d->user_id == Session::get('user_id')) { 
            $d->replies()->delete(); 
            $d->delete(); 
            return response()->json(['status' => 'success']); 
        } 
        return response()->json(['status' => 'error']); 
    }

    public function bantuan() { return view('user.bantuan'); }
    public function diskusi() { return view('user.diskusi'); }
    public function readNotification(string $id) 
    { 
        $notif = Notification::findOrFail($id); 
        $notif->update(['is_read' => 1]); 
        $materi = Material::find($notif->material_id); 
        return redirect('/belajar/' . $notif->course_id . '/' . $materi->urutan . '?tab=diskusi'); 
    }
}