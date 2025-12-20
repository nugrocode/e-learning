<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use App\Models\Concentration;
use App\Models\Course;
use App\Models\Material;
use App\Models\Progress;
use App\Models\Submission;
use App\Models\QuizScore;
use App\Models\QuizQuestion;

class LearningController extends Controller
{
    // 1. JALUR BELAJAR
    public function index()
    {
        $concentrations = Concentration::all();
        return view('user.jalur_belajar', compact('concentrations'));
    }

    // 2. MATA KULIAH
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

            if ($total_materi > 0) {
                $course->persen = round(($selesai_count / $total_materi) * 100);
            } else {
                $course->persen = 0;
            }
            $course->next_urutan = $next_urutan;
        }

        return view('user.mata_kuliah', compact('concentration', 'courses'));
    }

    // 3. KELAS SAYA
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

            if ($total_materi > 0) {
                $course->persen = round(($selesai_count / $total_materi) * 100);
            } else {
                $course->persen = 0;
            }
            $course->next_urutan = $next_urutan;
        }

        return view('user.kelas_saya', compact('courses'));
    }

    // 4. BELAJAR
    public function belajar($course_id, $urutan = 1)
    {
        $user_id = Session::get('user_id');
        $materi = Material::where('course_id', $course_id)->where('urutan', $urutan)->firstOrFail();

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

        if ($materi->kategori == 'quiz') {
            $data_nilai = QuizScore::where('user_id', $user_id)->where('material_id', $materi->id)->first();
            if (!$data_nilai || request('mode') == 'retake') {
                $soal_kuis = QuizQuestion::where('material_id', $materi->id)->inRandomOrder()->limit(5)->get();
            }
        } else {
            $data_tugas = Submission::where('user_id', $user_id)->where('material_id', $materi->id)->first();
        }

        return view('user.materi', compact('materi', 'daftar_materi', 'course_id', 'urutan', 'data_tugas', 'data_nilai', 'soal_kuis'));
    }

    // 5. PROSES SIMPAN
    public function storeProgress(Request $request)
    {
        $user_id = Session::get('user_id');
        $material_id = $request->material_id;
        $course_id = $request->course_id;
        $current_sequence = $request->urutan;

        $cek = Progress::where('user_id', $user_id)->where('material_id', $material_id)->first();
        if (!$cek) {
            Progress::create(['user_id' => $user_id, 'material_id' => $material_id, 'status' => 'selesai', 'tanggal_selesai' => now()]);
        }

        $next_seq = $current_sequence + 1;
        $next_material = Material::where('course_id', $course_id)->where('urutan', $next_seq)->exists();

        if ($next_material) {
            return redirect('/belajar/' . $course_id . '/' . $next_seq);
        } else {
            return redirect('/mata-kuliah/' . $course_id)->with('success', 'Selamat! Anda telah menyelesaikan kelas ini.');
        }
    }

    public function storeQuiz(Request $request)
    {
        $user_id = Session::get('user_id');
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
            ['user_id' => $user_id, 'material_id' => $request->material_id],
            ['skor' => $skor_akhir, 'tanggal_kerja' => now()]
        );

        return back()->with('success', 'Kuis Selesai! Skor Anda: ' . $skor_akhir);
    }

    public function storeAssignment(Request $request)
    {
        $request->validate(['link_github' => 'required|url']);
        Submission::create([
            'user_id' => Session::get('user_id'),
            'material_id' => $request->material_id,
            'file_path' => $request->link_github,
            'nilai' => 0
        ]);
        return back()->with('success', 'Tugas berhasil dikirim!');
    }

    public function bantuan()
    {
        return view('user.bantuan');
    }

    // 6. CHATBOT AI
    public function diskusi()
    {
        return view('user.diskusi');
    }

    public function askAi(Request $request)
    {
        $pesan_user = $request->input('message');
        $apiKey = env('GEMINI_API_KEY');

        if (!$apiKey) return response()->json(['reply' => "Error: API Key belum dipasang."]);

        $context = "Kamu adalah asisten dosen AI. Jawablah dengan format Markdown yang rapi.";

        try {
            // URL BERSIH (Pastikan tidak ada tanda kurung siku di sini)
            $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$apiKey}";

            $response = Http::withHeaders(['Content-Type' => 'application/json'])
                ->post($url, [
                'contents' => [['parts' => [['text' => $context . "\n\n Pertanyaan: " . $pesan_user]]]]
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $jawaban_ai = $data['candidates'][0]['content']['parts'][0]['text'] ?? "Maaf, saya bingung.";
                return response()->json(['reply' => $jawaban_ai]);
            } else {
                return response()->json(['reply' => "Gagal: " . $response->body()]);
            }
        } catch (\Exception $e) {
            return response()->json(['reply' => "Koneksi Error."]);
        }
    }

    // ==========================================================
    // 11. FITUR SPESIAL: AI CURRICULUM SORTER (ANTI ERROR)
    // ==========================================================
    public function autoSortKurikulum($concentration_id)
    {
        $apiKey = env('GEMINI_API_KEY');

        $courses = Course::where('concentration_id', $concentration_id)->get();

        if ($courses->isEmpty()) {
            return back()->with('error', 'Belum ada mata kuliah untuk diurutkan.');
        }

        $list_matkul = [];
        foreach($courses as $c) {
            $list_matkul[] = [
                'id' => $c->id,
                'nama_mk' => $c->nama_mk,
                'deskripsi' => $c->deskripsi
            ];
        }

        $json_input = json_encode($list_matkul);

        $context = "Urutkan daftar mata kuliah ini berdasarkan logika pembelajaran dari Dasar ke Mahir. " .
                   "Hanya return JSON array murni berisi ID yang sudah diurutkan. Jangan pakai markdown.";

        try {
            $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$apiKey}";

            // Retry 3x jika server Google sibuk
            $response = Http::retry(3, 2000)->withHeaders(['Content-Type' => 'application/json'])
                ->post($url, [
                    'contents' => [['parts' => [['text' => $context . "\n\n DATA: " . $json_input]]]]
                ]);

            if ($response->successful()) {
                $data = $response->json();
                $text_ai = $data['candidates'][0]['content']['parts'][0]['text'] ?? '';

                $clean_json = str_replace(['```json', '```'], '', $text_ai);
                $sorted_courses = json_decode($clean_json, true);

                if (json_last_error() !== JSON_ERROR_NONE) {
                    return back()->with('error', 'Gagal membaca respon AI. Format JSON tidak valid.');
                }

                $urutan_baru = 1;
                foreach ($sorted_courses as $item) {
                    // --- PERBAIKAN DI SINI ---
                    // Cek apakah item berupa array ['id'=>1] atau langsung angka 1
                    $id_course = is_array($item) ? $item['id'] : $item;

                    Course::where('id', $id_course)->update(['urutan' => $urutan_baru]);
                    $urutan_baru++;
                }

                return back()->with('success', 'Kurikulum berhasil disusun ulang oleh AI!');
            }

            return back()->with('error', 'Gagal terhubung ke AI: ' . $response->body());

        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }
}
