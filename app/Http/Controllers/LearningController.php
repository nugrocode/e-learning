<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use App\Models\Concentration;
use App\Models\Course;
use App\Models\Material;
use App\Models\Progress;
use App\Models\Submission;
use App\Models\QuizScore;
use App\Models\QuizQuestion;
use App\Models\Discussion;
use App\Models\User;
use App\Models\Notification; // Pastikan Model Notification di-import

class LearningController extends Controller
{
    // ==========================================================
    // 1. FITUR UTAMA PEMBELAJARAN (VIEW)
    // ==========================================================

    // Halaman Pilih Konsentrasi (Jalur Belajar)
    public function index()
    {
        $concentrations = Concentration::all();
        return view('user.jalur_belajar', compact('concentrations'));
    }

    // Halaman List Mata Kuliah
    public function showCourses($id)
    {
        $concentration = Concentration::findOrFail($id);
        $courses = Course::where('concentration_id', $id)->orderBy('urutan', 'asc')->get();
        $user_id = Session::get('user_id');

        foreach ($courses as $course) {
            $materials = Material::where('course_id', $course->id)->orderBy('urutan', 'asc')->get();
            $total_materi = $materials->count();
            $selesai_count = 0;
            $next_urutan = 1;
            $found_next = false;

            foreach ($materials as $m) {
                $is_done = Progress::where('user_id', $user_id)->where('material_id', $m->id)->exists();
                if ($is_done) {
                    $selesai_count++;
                } else {
                    if (!$found_next) {
                        $next_urutan = $m->urutan;
                        $found_next = true;
                    }
                }
            }

            $course->persen = $total_materi > 0 ? round(($selesai_count / $total_materi) * 100) : 0;
            $course->next_urutan = $next_urutan;
        }

        return view('user.mata_kuliah', compact('concentration', 'courses'));
    }

    // Halaman Kelas Saya (Progress)
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
                if ($is_done) {
                    $selesai_count++;
                } else {
                    if (!$found_next) {
                        $next_urutan = $m->urutan;
                        $found_next = true;
                    }
                }
            }

            $course->persen = $total_materi > 0 ? round(($selesai_count / $total_materi) * 100) : 0;
            $course->next_urutan = $next_urutan;
        }

        return view('user.kelas_saya', compact('courses'));
    }

    // HALAMAN MATERI (BELAJAR)
    public function belajar($course_id, $urutan = 1)
    {
        $user_id = Session::get('user_id');
        $materi = Material::where('course_id', $course_id)->where('urutan', $urutan)->firstOrFail();

        // Validasi urutan (Cek materi sebelumnya)
        if ($urutan > 1) {
            $prev_materi = Material::where('course_id', $course_id)->where('urutan', $urutan - 1)->first();
            if ($prev_materi) {
                $cek_progress = Progress::where('user_id', $user_id)->where('material_id', $prev_materi->id)->exists();
                if (!$cek_progress) {
                    return redirect()->back()->with('error', 'Selesaikan materi sebelumnya dulu!');
                }
            }
        }

        $daftar_materi = Material::where('course_id', $course_id)->orderBy('urutan', 'asc')->get();
        $data_tugas = null;
        $data_nilai = null;
        $soal_kuis = [];

        // Logic Kuis & Tugas
        if ($materi->kategori == 'quiz') {
            $data_nilai = QuizScore::where('user_id', $user_id)->where('material_id', $materi->id)->first();
            if (!$data_nilai || request('mode') == 'retake') {
                $soal_kuis = QuizQuestion::where('material_id', $materi->id)->inRandomOrder()->limit(5)->get();
            }
        } else {
            $data_tugas = Submission::where('user_id', $user_id)->where('material_id', $materi->id)->first();
        }

        // AMBIL DATA DISKUSI (Threaded)
        // Ambil parent comment saja, tapi load replies beserta usernya
        $diskusi = Discussion::with(['user', 'replies.user'])
                    ->where('material_id', $materi->id)
                    ->whereNull('parent_id')
                    ->orderBy('created_at', 'desc')
                    ->get();

        return view('user.materi', compact('materi', 'daftar_materi', 'course_id', 'urutan', 'data_tugas', 'data_nilai', 'soal_kuis', 'diskusi'));
    }

    // ==========================================================
    // 2. PROSES LOGIKA BELAJAR (POST)
    // ==========================================================

    public function storeProgress(Request $request)
    {
        $user_id = Session::get('user_id');
        $cek = Progress::where('user_id', $user_id)->where('material_id', $request->material_id)->first();
        if (!$cek) {
            Progress::create(['user_id' => $user_id, 'material_id' => $request->material_id, 'status' => 'selesai', 'tanggal_selesai' => now()]);
        }

        $next_seq = $request->urutan + 1;
        if (Material::where('course_id', $request->course_id)->where('urutan', $next_seq)->exists()) {
            return redirect('/belajar/' . $request->course_id . '/' . $next_seq);
        } else {
            return redirect('/mata-kuliah/' . $request->course_id)->with('success', 'Selamat! Anda telah menyelesaikan kelas ini.');
        }
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
        QuizScore::updateOrCreate(
            ['user_id' => Session::get('user_id'), 'material_id' => $request->material_id],
            ['skor' => $skor_akhir, 'tanggal_kerja' => now()]
        );

        return back()->with('success', 'Kuis Selesai! Skor Anda: ' . $skor_akhir);
    }

    public function storeAssignment(Request $request)
    {
        $user_id = Session::get('user_id');

        if ($request->hasFile('file_tugas')) {
            $request->validate(['file_tugas' => 'required|file|mimes:pdf,doc,docx,zip,rar|max:10240']);
            $file = $request->file('file_tugas');
            $filename = time() . '_' . $user_id . '_' . $file->getClientOriginalName();
            $path = public_path('uploads/submissions');
            if(!file_exists($path)) mkdir($path, 0777, true);
            $file->move($path, $filename);
            $simpan_path = $filename;
        } else {
            $request->validate(['file_tugas' => 'required|url']);
            $simpan_path = $request->file_tugas;
        }

        Submission::create([
            'user_id' => $user_id,
            'material_id' => $request->material_id,
            'file_path' => $simpan_path,
            'nilai' => 0
        ]);

        return back()->with('success', 'Tugas berhasil dikirim!');
    }

    // ==========================================================
    // 3. FITUR DISKUSI & NOTIFIKASI
    // ==========================================================

    // Simpan Diskusi (AJAX) + Buat Notifikasi
    public function storeDiscussion(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
            'parent_id' => 'nullable|integer'
        ]);

        // 1. Simpan Komentar
        $diskusi = Discussion::create([
            'course_id' => $request->course_id,
            'material_id' => $request->material_id,
            'user_id' => Session::get('user_id'),
            'message' => $request->message,
            'parent_id' => $request->parent_id
        ]);

        // 2. Buat Notifikasi (Jika ini balasan)
        if ($request->parent_id) {
            $parentComment = Discussion::find($request->parent_id);
            // Jangan kirim notif jika membalas diri sendiri
            if ($parentComment && $parentComment->user_id != Session::get('user_id')) {
                $sender = User::find(Session::get('user_id'));

                Notification::create([
                    'user_id'     => $parentComment->user_id, // Penerima (Pemilik komentar asli)
                    'sender_id'   => $sender->id,             // Pengirim (Yang membalas)
                    'material_id' => $request->material_id,
                    'course_id'   => $request->course_id,
                    'message'     => $sender->nama_lengkap . " membalas komentar Anda.",
                    'is_read'     => 0
                ]);
            }
        }

        $diskusi->load('user');

        return response()->json([
            'status' => 'success',
            'message' => 'Komentar berhasil dikirim!',
            'data' => [
                'id' => $diskusi->id,
                'nama' => $diskusi->user->nama_lengkap,
                'foto' => $diskusi->user->foto_profil,
                'role' => $diskusi->user->role,
                'isi'  => $diskusi->message,
                'waktu' => 'Baru saja',
                'parent_id' => $diskusi->parent_id
            ]
        ]);
    }

    // Hapus Diskusi
    public function destroyDiscussion($id)
    {
        $diskusi = Discussion::find($id);

        if (!$diskusi) return response()->json(['status' => 'error', 'message' => 'Komentar tidak ditemukan.']);

        // Cek Hak Akses (Hanya pemilik)
        if ($diskusi->user_id != Session::get('user_id')) {
            return response()->json(['status' => 'error', 'message' => 'Anda tidak berhak menghapus komentar ini.']);
        }

        $diskusi->replies()->delete(); // Hapus balasan dulu
        $diskusi->delete();

        return response()->json(['status' => 'success', 'message' => 'Komentar berhasil dihapus.']);
    }

    // Baca Notifikasi (Klik dari Navbar)
    public function readNotification($id)
    {
        $notif = Notification::findOrFail($id);

        // Tandai sudah dibaca
        $notif->update(['is_read' => 1]);

        // Redirect ke materi terkait
        $materi = Material::find($notif->material_id);

        return redirect('/belajar/' . $notif->course_id . '/' . $materi->urutan . '?tab=diskusi');
    }

    // ==========================================================
    // 4. FITUR PROFIL PENGGUNA
    // ==========================================================

    public function editProfile()
    {
        $user = User::find(Session::get('user_id'));
        return view('user.profil', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = User::find(Session::get('user_id'));

        $request->validate([
            'nama_lengkap' => 'required|string|max:100',
            'foto'         => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'password'     => 'nullable|min:6'
        ]);

        $user->nama_lengkap = $request->nama_lengkap;
        Session::put('nama', $request->nama_lengkap);

        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('images'), $filename);

            if ($user->foto_profil && $user->foto_profil != 'default.png' && file_exists(public_path('images/' . $user->foto_profil))) {
                unlink(public_path('images/' . $user->foto_profil));
            }

            $user->foto_profil = $filename;
            Session::put('foto', $filename);
        }

        if ($request->filled('password')) {
            $user->password = md5($request->password);
        }

        $user->save();
        return back()->with('success', 'Profil berhasil diperbarui!');
    }

    // ==========================================================
    // 5. FITUR TAMBAHAN (AI & BANTUAN)
    // ==========================================================

    public function bantuan()
    {
        return view('user.bantuan');
    }

    public function diskusi()
    {
        return view('user.diskusi');
    }

    public function askAi(Request $request)
    {
        $pesan_user = $request->input('message');
        $apiKey = env('GEMINI_API_KEY');

        if (!$apiKey) return response()->json(['reply' => "Error: API Key belum dipasang."]);

        try {
            $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$apiKey}";
            $response = Http::withHeaders(['Content-Type' => 'application/json'])->post($url, [
                'contents' => [['parts' => [['text' => "Jawablah dengan format Markdown. Pertanyaan: " . $pesan_user]]]]
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $jawaban = $data['candidates'][0]['content']['parts'][0]['text'] ?? "Maaf, saya bingung.";
                return response()->json(['reply' => $jawaban]);
            }
            return response()->json(['reply' => "Gagal: " . $response->body()]);
        } catch (\Exception $e) {
            return response()->json(['reply' => "Koneksi Error."]);
        }
    }

    public function autoSortKurikulum($concentration_id)
    {
        $apiKey = env('GEMINI_API_KEY');
        $courses = Course::where('concentration_id', $concentration_id)->get();

        if ($courses->isEmpty()) return back()->with('error', 'Mata kuliah kosong.');

        $list = [];
        foreach($courses as $c) $list[] = ['id' => $c->id, 'nama' => $c->nama_mk, 'deskripsi' => $c->deskripsi];

        $context = "Urutkan mata kuliah ini dari Dasar ke Mahir. Return JSON Array ID saja. Jangan pakai Markdown.";

        try {
            $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$apiKey}";
            $response = Http::retry(3, 2000)->withHeaders(['Content-Type' => 'application/json'])->post($url, [
                'contents' => [['parts' => [['text' => $context . "\n\n DATA: " . json_encode($list)]]]]
            ]);

            if ($response->successful()) {
                $text = $response->json()['candidates'][0]['content']['parts'][0]['text'] ?? '';
                $clean = str_replace(['```json', '```'], '', $text);
                $ids = json_decode($clean, true);

                if (json_last_error() === JSON_ERROR_NONE) {
                    $urutan = 1;
                    foreach ($ids as $item) {
                        $id = is_array($item) ? $item['id'] : $item;
                        Course::where('id', $id)->update(['urutan' => $urutan++]);
                    }
                    return back()->with('success', 'Kurikulum berhasil disusun ulang!');
                }
            }
            return back()->with('error', 'Gagal menyusun kurikulum.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
}
