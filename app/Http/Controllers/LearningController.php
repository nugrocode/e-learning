<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash; 
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
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

class LearningController extends Controller
{
    // ==========================================================
    // BAGIAN 1: LOGIKA AI (CORE ENGINE)
    // ==========================================================

    private function askGeminiJSON($prompt, $contextData) {
        $apiKey = env('GEMINI_API_KEY');
        $model = env('GEMINI_MODEL', 'gemini-2.5-flash');
        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";

        $systemInstruction = "
            Anda adalah Arsitek Kurikulum Universitas. Tugas: Urutkan mata kuliah secara logis (Prerequisite Chain).
            ATURAN: Output HARUS JSON Array murni valid berisi ID. Jangan pakai Markdown.
        ";

        $fullPrompt = $systemInstruction . "\n\nINSTRUKSI: " . $prompt . "\n\nDATA KONTEKS:\n" . json_encode($contextData);

        try {
            $response = Http::withHeaders(['Content-Type' => 'application/json'])
                ->post($url, ['contents' => [['parts' => [['text' => $fullPrompt]]]]]);
            
            if ($response->successful()) {
                $text = $response->json()['candidates'][0]['content']['parts'][0]['text'] ?? '[]';
                $cleanText = str_replace(['```json', '```', "\n"], '', $text);
                return json_decode($cleanText, true);
            }
        } catch (\Exception $e) { return null; }
        return null;
    }

    // 1. Reset Total: Urutkan ulang MK di Konsentrasi tertentu
    public function adminResetKurikulum($concentration_id) {
        $konsentrasi = Concentration::findOrFail($concentration_id);
        $courses = $konsentrasi->courses()->get(); 

        if ($courses->isEmpty()) return back()->with('error', 'Data kosong.');

        $data = $courses->map(function($c) { return ['id' => $c->id, 'nama' => $c->nama_mk, 'deskripsi' => $c->deskripsi]; });
        $prompt = "Urutkan Mata Kuliah ini dari tingkat Dasar ke Mahir (Semester 1 sampai Akhir). Return JSON Array of IDs saja.";
        
        $sortedIDs = $this->askGeminiJSON($prompt, $data);

        if ($sortedIDs && is_array($sortedIDs)) {
            foreach ($sortedIDs as $index => $course_id) { 
                $konsentrasi->courses()->updateExistingPivot($course_id, ['urutan' => $index + 1]);
            }
            return back()->with('success', 'Kurikulum berhasil disusun ulang oleh AI!');
        }
        return back()->with('error', 'Gagal menghubungi AI.');
    }

    // 2. Smart Insert: Menyisipkan MK baru ke posisi logis
    public function adminUpdateKurikulum($concentration_id) {
        $konsentrasi = Concentration::findOrFail($concentration_id);
        
        $existing = $konsentrasi->courses()->wherePivot('urutan', '>', 0)->orderBy('pivot_urutan', 'asc')->get(['courses.id', 'nama_mk']);
        $pending = $konsentrasi->courses()->wherePivot('urutan', 0)->get();

        if ($pending->isEmpty()) return back()->with('error', 'Tidak ada MK baru yang perlu disisipkan.');

        foreach ($pending as $newCourse) {
            $prompt = "Kurikulum saat ini: " . json_encode($existing) . ". \n\nMK Baru: " . $newCourse->nama_mk . " (" . $newCourse->deskripsi . "). \n\nPERTANYAAN: Setelah ID mana MK ini disisipkan agar urutannya logis? Jawab JSON: {'insert_after_id': ID_TARGET}. (Jawab 0 jika paling awal).";
            
            $result = $this->askGeminiJSON($prompt, []); 
            $targetId = $result['insert_after_id'] ?? null;

            if ($targetId !== null) {
                $targetUrutan = 0;
                if ($targetId != 0) { 
                    $ref = DB::table('concentration_course')
                        ->where('concentration_id', $concentration_id)
                        ->where('course_id', $targetId)
                        ->first();
                    if ($ref) $targetUrutan = $ref->urutan; 
                }
                
                DB::table('concentration_course')
                    ->where('concentration_id', $concentration_id)
                    ->where('urutan', '>', $targetUrutan)
                    ->increment('urutan');
                
                $konsentrasi->courses()->updateExistingPivot($newCourse->id, ['urutan' => $targetUrutan + 1]);
                $existing = $konsentrasi->courses()->wherePivot('urutan', '>', 0)->orderBy('pivot_urutan', 'asc')->get(['courses.id', 'nama_mk']);
            }
        }
        return back()->with('success', 'Mata Kuliah baru berhasil disisipkan secara cerdas!');
    }

    // 3. Batch Auto Distribute (Tombol Sakti)
    public function adminAutoDistribute() {
        $apiKey = env('GEMINI_API_KEY');
        $model = env('GEMINI_MODEL', 'gemini-2.5-flash');
        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";

        $concentrations = Concentration::all();
        $courses = Course::all(); 

        if ($courses->isEmpty()) return back()->with('error', 'Belum ada Mata Kuliah untuk didistribusikan.');

        $listKonsentrasi = $concentrations->map(function($c) { return "ID: {$c->id}, Nama: {$c->nama_konsentrasi}"; })->implode("\n");
        $listCourses = $courses->map(function($c) { return "ID: {$c->id}, Nama: {$c->nama_mk}, Deskripsi: " . Str::limit($c->deskripsi, 100); })->implode("\n");

        $prompt = "
            Role: Kaprodi Teknik Informatika.
            Tugas: Distribusikan Mata Kuliah ke Konsentrasi yang relevan.
            
            DAFTAR KONSENTRASI (TARGET):
            {$listKonsentrasi}
            
            DAFTAR MATA KULIAH (SOURCE):
            {$listCourses}
            
            ATURAN LOGIKA:
            1. MK PONDASI IT (Algoritma, Database, Web Dasar, Jaringan, Etika, Math, dll) -> Masukkan ke SEMUA ID Konsentrasi.
            2. MK SPESIFIK -> Hanya ke ID Konsentrasi yang sesuai topiknya (Misal: 'Android' ke Software, 'Sensor' ke IoT).
            
            OUTPUT HARUS JSON FORMAT INI:
            [
                { \"course_id\": 1, \"target_concentration_ids\": [1, 2, 3] },
                { \"course_id\": 2, \"target_concentration_ids\": [2] }
            ]
        ";

        try {
            $response = Http::withHeaders(['Content-Type' => 'application/json'])
                ->post($url, ['contents' => [['parts' => [['text' => $prompt]]]]]);
            
            if ($response->successful()) {
                $text = $response->json()['candidates'][0]['content']['parts'][0]['text'] ?? '[]';
                $cleanText = str_replace(['```json', '```', "\n"], '', $text);
                $mappings = json_decode($cleanText, true);

                if (is_array($mappings)) {
                    $count = 0;
                    foreach ($mappings as $map) {
                        $courseId = $map['course_id'];
                        $targets = $map['target_concentration_ids'];

                        foreach ($targets as $concId) {
                            $exists = DB::table('concentration_course')
                                ->where('concentration_id', $concId)
                                ->where('course_id', $courseId)
                                ->exists();

                            if (!$exists) {
                                DB::table('concentration_course')->insert([
                                    'concentration_id' => $concId,
                                    'course_id' => $courseId,
                                    'urutan' => 0, 
                                    'created_at' => now(),
                                    'updated_at' => now()
                                ]);
                                $count++;
                            }
                        }
                    }
                    return back()->with('success', "AI berhasil mendistribusikan {$count} item hubungan MK-Prodi!");
                }
            }
        } catch (\Exception $e) { 
            return back()->with('error', 'Gagal koneksi ke AI: ' . $e->getMessage()); 
        }
        return back()->with('error', 'AI tidak memberikan respons valid.');
    }


    // ==========================================================
    // BAGIAN 2: MANAJEMEN USER (UX FIXED)
    // ==========================================================

    public function adminUsersIndex(Request $request) {
        // UX FIX: Jika tidak ada role filter, redirect otomatis ke Mahasiswa
        if (!$request->has('role')) {
            return redirect('/admin/users?role=mahasiswa');
        }

        $query = User::query();

        // 1. LOGIKA SEARCH (Cari Nama atau NIM/NIDN)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_lengkap', 'LIKE', "%{$search}%")
                  ->orWhere('nim_nidn', 'LIKE', "%{$search}%");
            });
        }

        // 2. LOGIKA FILTER ROLE (Wajib)
        $query->where('role', $request->role);

        // Pagination
        $users = $query->latest()->paginate(10)->withQueryString();

        // Stats Counter
        $stats = [
            'total' => User::count(),
            'dosen' => User::where('role', 'dosen')->count(),
            'mahasiswa' => User::where('role', 'mahasiswa')->count(),
            'admin' => User::where('role', 'admin')->count(),
        ];

        return view('admin.users.index', compact('users', 'stats'));
    }

    public function adminUserStore(Request $request) {
        $request->validate([
            'nim' => 'required|unique:users,nim_nidn', // Validasi ke kolom nim_nidn
            'nama_lengkap' => 'required',
            'role' => 'required',
            'password' => 'required|min:4',
        ]);

        $filename = null;
        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('images'), $filename);
        }

        User::create([
            'nim_nidn' => $request->nim, // Map ke nim_nidn
            'nama_lengkap' => $request->nama_lengkap,
            'password' => md5($request->password), // MD5
            'role' => $request->role,
            'foto_profil' => $filename
        ]);

        return back()->with('success', 'Pengguna baru berhasil ditambahkan!');
    }

    public function adminUserUpdate(Request $request, $id) {
        $user = User::findOrFail($id);
        $request->validate([
            'nim' => 'required|unique:users,nim_nidn,'.$id,
            'nama_lengkap' => 'required',
            'role' => 'required',
        ]);

        if ($request->filled('password')) {
            $user->password = md5($request->password);
        }

        if ($request->hasFile('foto')) {
            if ($user->foto_profil && file_exists(public_path('images/'.$user->foto_profil))) {
                unlink(public_path('images/'.$user->foto_profil));
            }
            $file = $request->file('foto');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('images'), $filename);
            $user->foto_profil = $filename;
        }

        $user->nim_nidn = $request->nim;
        $user->nama_lengkap = $request->nama_lengkap;
        $user->role = $request->role;
        $user->save();

        return back()->with('success', 'Data pengguna diperbarui!');
    }

    public function adminUserDestroy($id) {
        // Cek agar tidak menghapus diri sendiri
        if ($id == Session::get('user_id')) {
            return back()->with('error', 'Anda tidak dapat menghapus akun sendiri!');
        }

        $user = User::findOrFail($id);

        // --- CLEANUP DATA TERKAIT (SOLUSI FK CONSTRAINT) ---
        Progress::where('user_id', $id)->delete();
        Submission::where('user_id', $id)->delete();
        QuizScore::where('user_id', $id)->delete();
        Discussion::where('user_id', $id)->delete();
        Notification::where('user_id', $id)->orWhere('sender_id', $id)->delete();
        Course::where('dosen_id', $id)->update(['dosen_id' => null]);

        // Hapus File
        if ($user->foto_profil && file_exists(public_path('images/'.$user->foto_profil))) {
            unlink(public_path('images/'.$user->foto_profil));
        }
        
        $user->delete();
        return back()->with('success', 'Pengguna berhasil dihapus.');
    }


    // ==========================================================
    // BAGIAN 3: MASTER DATA - BANK MATA KULIAH
    // ==========================================================

    public function adminBankIndex() {
        $courses = Course::with('dosen')->latest()->get();
        $dosens = User::where('role', 'dosen')->get();
        return view('admin.bank_mk.index', compact('courses', 'dosens'));
    }

    public function adminBankStore(Request $request) {
        $request->validate([
            'nama_mk' => 'required|unique:courses,nama_mk',
            'dosen_id' => 'required',
        ]);

        $filename = null;
        if ($request->hasFile('gambar')) {
            $file = $request->file('gambar');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('images'), $filename);
        }

        Course::create([
            'nama_mk' => $request->nama_mk,
            'dosen_id' => $request->dosen_id,
            'deskripsi' => $request->deskripsi,
            'gambar' => $filename
        ]);

        return back()->with('success', 'Mata Kuliah baru berhasil ditambahkan ke Bank Data.');
    }

    public function adminBankUpdate(Request $request, $id) {
        $c = Course::findOrFail($id);
        $request->validate([
            'nama_mk' => 'required|unique:courses,nama_mk,'.$id,
            'dosen_id' => 'required',
        ]);

        if ($request->hasFile('gambar')) {
            if ($c->gambar && file_exists(public_path('images/'.$c->gambar))) unlink(public_path('images/'.$c->gambar));
            $file = $request->file('gambar');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('images'), $filename);
            $c->gambar = $filename;
        }

        $c->update($request->except('gambar'));
        return back()->with('success', 'Data Master Mata Kuliah diperbarui.');
    }

    public function adminBankDestroy($id) {
        $c = Course::findOrFail($id);
        if ($c->gambar && file_exists(public_path('images/'.$c->gambar))) unlink(public_path('images/'.$c->gambar));
        $c->concentrations()->detach();
        $c->delete(); 
        return back()->with('success', 'Mata Kuliah dihapus permanen dari sistem.');
    }


    // ==========================================================
    // BAGIAN 4: DISTRIBUSI KURIKULUM (PIVOT)
    // ==========================================================

    public function adminDashboard() {
        $total_mhs = User::where('role', 'mahasiswa')->count();
        $total_dosen = User::where('role', 'dosen')->count();
        $total_konsentrasi = Concentration::count();
        $total_mk = Course::count();
        $recent_users = User::latest()->take(5)->get();
        return view('admin.dashboard', compact('total_mhs', 'total_dosen', 'total_konsentrasi', 'total_mk', 'recent_users'));
    }

    // --- PENGUMUMAN ---
    public function adminPengumumanIndex() { $pengumuman = Announcement::latest()->get(); return view('admin.pengumuman.index', compact('pengumuman')); }
    public function adminPengumumanStore(Request $request) { Announcement::create($request->all() + ['is_active'=>1]); return back()->with('success', 'Pengumuman diterbitkan!'); }
    public function adminPengumumanUpdate(Request $request, $id) { Announcement::findOrFail($id)->update($request->all()); return back()->with('success', 'Pengumuman diperbarui!'); }
    public function adminPengumumanDestroy($id) { Announcement::findOrFail($id)->delete(); return back()->with('success', 'Pengumuman dihapus.'); }

    // --- KONSENTRASI ---
    public function adminKonsentrasiIndex() {
        $konsentrasi = Concentration::all();
        foreach($konsentrasi as $k) { $k->total_mk = $k->courses()->count(); }
        return view('admin.konsentrasi.index', compact('konsentrasi'));
    }
    public function adminKonsentrasiStore(Request $request) {
        $filename = null;
        if ($request->hasFile('gambar')) { $file=$request->file('gambar'); $filename=time().'_'.$file->getClientOriginalName(); $file->move(public_path('images'), $filename); }
        Concentration::create(['nama_konsentrasi'=>$request->nama_konsentrasi, 'deskripsi'=>$request->deskripsi, 'gambar'=>$filename]);
        return back()->with('success', 'Prodi ditambahkan!');
    }
    public function adminKonsentrasiUpdate(Request $request, $id) {
        $k = Concentration::findOrFail($id);
        if ($request->hasFile('gambar')) { $file=$request->file('gambar'); $filename=time().'_'.$file->getClientOriginalName(); $file->move(public_path('images'), $filename); $k->gambar=$filename; }
        $k->update($request->except('gambar'));
        return back()->with('success', 'Data diperbarui!');
    }
    public function adminKonsentrasiDestroy($id) { Concentration::findOrFail($id)->delete(); return back()->with('success', 'Prodi dihapus.'); }

    // --- KURIKULUM & MK ---
    
    public function adminKurikulumIndex() {
        $konsentrasi = Concentration::all();
        foreach($konsentrasi as $k) { $k->total_mk = $k->courses()->count(); }
        return view('admin.kurikulum.index', compact('konsentrasi'));
    }

    public function adminKurikulumShow($id) {
        $konsentrasi = Concentration::findOrFail($id);
        $dosens = User::where('role', 'dosen')->get();

        $courses = $konsentrasi->courses()
            ->with('dosen') 
            ->wherePivot('urutan', '>', 0)
            ->orderBy('concentration_course.urutan', 'asc') 
            ->get();
        
        $new_courses = $konsentrasi->courses()
            ->wherePivot('urutan', 0)
            ->get();

        return view('admin.kurikulum.detail', compact('konsentrasi', 'courses', 'new_courses', 'dosens'));
    }

    public function adminCourseStore(Request $request) {
        $request->validate([
            'concentration_id' => 'required',
            'nama_mk' => 'required',
        ]);

        $course = Course::where('nama_mk', $request->nama_mk)->first();

        if (!$course) {
            $request->validate(['dosen_id' => 'required']);
            $filename = null;
            if ($request->hasFile('gambar')) {
                $file = $request->file('gambar');
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('images'), $filename);
            }
            
            $course = Course::create([
                'nama_mk' => $request->nama_mk,
                'dosen_id' => $request->dosen_id,
                'deskripsi' => $request->deskripsi,
                'gambar' => $filename
            ]);
        }

        $exists = DB::table('concentration_course')
            ->where('concentration_id', $request->concentration_id)
            ->where('course_id', $course->id)
            ->exists();
        
        if (!$exists) {
            $konsentrasi = Concentration::find($request->concentration_id);
            $konsentrasi->courses()->attach($course->id, ['urutan' => 0]);
            return back()->with('success', 'Mata Kuliah ditambahkan (Manual). Gunakan tombol "AI Smart Distribute" untuk menyebar ke prodi lain.');
        }

        return back()->with('error', 'Mata Kuliah sudah ada di sini.');
    }

    public function adminCourseUpdate(Request $request, $id) {
        $c = Course::findOrFail($id);
        
        if ($request->hasFile('gambar')) { 
            $file=$request->file('gambar'); 
            $filename=time().'_'.$file->getClientOriginalName(); 
            $file->move(public_path('images'), $filename); 
            $c->gambar=$filename; 
        }
        
        $c->update($request->except('gambar')); 
        return back()->with('success', 'Master Mata Kuliah diperbarui (Berlaku global).');
    }

    public function adminCourseDestroy($id) {
        $concentration_id = request('concentration_id'); 
        
        if($concentration_id) {
            $konsentrasi = Concentration::find($concentration_id);
            $konsentrasi->courses()->detach($id); 
            return back()->with('success', 'Mata Kuliah dihapus dari Konsentrasi ini (Data Master tetap aman).');
        }
        return back()->with('error', 'Gagal menghapus.');
    }


    // ==========================================================
    // BAGIAN 5: FITUR USER / MAHASISWA
    // ==========================================================

    public function dashboard() { 
        $announcements = Announcement::where('is_active', true)->latest()->get(); 
        return view('user.dashboard', compact('announcements')); 
    }
    
    public function index() {
        $concentrations = Concentration::all();
        foreach($concentrations as $k) { $k->total_mk = $k->courses()->count(); }
        return view('user.jalur_belajar', compact('concentrations'));
    }

    public function showCourses($id) {
        // [FIX 1] SIMPAN ID KONSENTRASI KE SESSION
        // Agar saat di halaman materi, sistem tahu user berasal dari prodi mana
        Session::put('active_concentration_id', $id);

        $concentration = Concentration::findOrFail($id);
        
        // Ambil MK berdasarkan urutan pivot
        $courses = $concentration->courses()
                        ->with('dosen')
                        ->wherePivot('urutan', '>', 0)
                        ->orderBy('concentration_course.urutan', 'asc') 
                        ->get();
                        
        $user_id = Session::get('user_id');

        // VARIABLE PENTING UNTUK LOGIKA BERURUTAN
        $is_previous_completed = true; // MK pertama dianggap terbuka (kecuali materi kosong)

        foreach ($courses as $course) {
            // Hitung Materi & Progress
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

            // Hitung Persentase
            $course->persen = $total_materi > 0 ? round(($selesai_count / $total_materi) * 100) : 0;
            $course->next_urutan = $next_urutan;
            $course->total_materi = $total_materi;

            // [FIX 2] LOGIKA LOCK / PENGUNCIAN YANG LEBIH KETAT
            if ($total_materi == 0) {
                // KASUS A: Materi Kosong -> KUNCI MUTLAK
                $course->status_akses = 'empty'; 
                $course->pesan_kunci = 'Materi belum diinput oleh Dosen.';
            } 
            elseif (!$is_previous_completed) {
                // KASUS B: MK Sebelumnya Belum 100% -> KUNCI
                $course->status_akses = 'locked';
                $course->pesan_kunci = 'Selesaikan mata kuliah sebelumnya.';
            } 
            else {
                // KASUS C: Aman -> BUKA
                $course->status_akses = 'open';
            }

            // Update status untuk iterasi MK berikutnya
            // MK berikutnya hanya boleh terbuka jika MK saat ini sudah 100%
            // DAN MK saat ini tidak kosong materinya.
            if ($total_materi > 0 && $course->persen == 100) {
                $is_previous_completed = true;
            } else {
                $is_previous_completed = false;
            }
        }

        return view('user.mata_kuliah', compact('concentration', 'courses'));
    }

    public function belajar($course_id, $urutan = 1) {
        $user_id = Session::get('user_id');
        
        // Eager load 'course' untuk tombol kembali
        $materi = Material::with('course')->where('course_id', $course_id)->where('urutan', $urutan)->firstOrFail();
        
        // Logika Prasyarat Materi
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
        
        $diskusi = Discussion::with(['user', 'replies.user'])->where('material_id', $materi->id)->whereNull('parent_id')->orderBy('created_at', 'desc')->get();
        
        return view('user.materi', compact('materi', 'daftar_materi', 'course_id', 'urutan', 'data_tugas', 'data_nilai', 'soal_kuis', 'diskusi'));
    }
    
    public function myClasses() {
        $user_id = Session::get('user_id');
        $courses = Course::whereHas('materials.progress', function($q) use ($user_id) { $q->where('user_id', $user_id); })->get();
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

    public function storeProgress(Request $request) {
        $user_id = Session::get('user_id');
        Progress::firstOrCreate(['user_id' => $user_id, 'material_id' => $request->material_id], ['status' => 'selesai', 'tanggal_selesai' => now()]);
        $next = $request->urutan + 1;
        if (Material::where('course_id', $request->course_id)->where('urutan', $next)->exists()) return redirect('/belajar/' . $request->course_id . '/' . $next);
        return redirect('/mata-kuliah/' . $request->course_id)->with('success', 'Kelas Selesai!');
    }

    public function storeQuiz(Request $request) {
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
        return redirect('/belajar/' . $request->course_id . '/' . $request->urutan)->with('success', 'Kuis Selesai! Skor Anda: ' . $skor_akhir);
    }

    public function storeAssignment(Request $request) {
        $user_id = Session::get('user_id');
        $path = $request->file_tugas;
        if ($request->hasFile('file_tugas')) { $file = $request->file('file_tugas'); $filename = time() . '_' . $user_id . '_' . $file->getClientOriginalName(); $file->move(public_path('uploads/submissions'), $filename); $path = $filename; }
        Submission::create(['user_id' => $user_id, 'material_id' => $request->material_id, 'file_path' => $path, 'nilai' => 0]);
        return back()->with('success', 'Tugas dikirim!');
    }

    // --- FITUR TAMBAHAN (Chat AI, Profil, Dosen) ---
    public function askAi(Request $request) {
        $pesan = $request->input('message'); $apiKey = env('GEMINI_API_KEY');
        $prompt = "Role: Asisten Gaul Jaksel. User: $pesan";
        try {
            $model = env('GEMINI_MODEL', 'gemini-2.5-flash');
            $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";
            $response = Http::withHeaders(['Content-Type' => 'application/json'])->post($url, ['contents' => [['parts' => [['text' => $prompt]]]]]);
            if ($response->successful()) { return response()->json(['reply' => $response->json()['candidates'][0]['content']['parts'][0]['text']]); }
        } catch (\Exception $e) { }
        return response()->json(['reply' => "Sorry, error connection."]);
    }

    public function editProfile() { $user = User::find(Session::get('user_id')); return view('user.profil', compact('user')); }
    public function updateProfile(Request $request) { 
        $user = User::find(Session::get('user_id')); $user->nama_lengkap = $request->nama_lengkap; Session::put('nama', $request->nama_lengkap); 
        if($request->hasFile('foto')) { $file = $request->file('foto'); $filename = time().'_'.$file->getClientOriginalName(); $file->move(public_path('images'), $filename); $user->foto_profil = $filename; Session::put('foto', $filename); } 
        if($request->filled('password')) { $user->password = md5($request->password); } $user->save(); return back()->with('success', 'Profil update'); 
    }
    public function bantuan() { return view('user.bantuan'); }
    public function diskusi() { return view('user.diskusi'); }
    public function storeDiscussion(Request $request) { $diskusi = Discussion::create(['course_id' => $request->course_id, 'material_id' => $request->material_id, 'user_id' => Session::get('user_id'), 'message' => $request->message, 'parent_id' => $request->parent_id]); if ($request->parent_id) { $parent = Discussion::find($request->parent_id); if ($parent && $parent->user_id != Session::get('user_id')) { Notification::create(['user_id' => $parent->user_id, 'sender_id' => Session::get('user_id'), 'material_id' => $request->material_id, 'course_id' => $request->course_id, 'message' => "Membalas komentar Anda.", 'is_read' => 0]); } } $diskusi->load('user'); return response()->json(['status' => 'success', 'data' => ['id' => $diskusi->id, 'nama' => $diskusi->user->nama_lengkap, 'role' => $diskusi->user->role, 'foto' => $diskusi->user->foto_profil, 'isi' => $diskusi->message]]); }
    public function destroyDiscussion($id) { $d = Discussion::find($id); if ($d && $d->user_id == Session::get('user_id')) { $d->replies()->delete(); $d->delete(); return response()->json(['status' => 'success']); } return response()->json(['status' => 'error']); }
    public function readNotification($id) { $notif = Notification::findOrFail($id); $notif->update(['is_read' => 1]); $materi = Material::find($notif->material_id); return redirect('/belajar/' . $notif->course_id . '/' . $materi->urutan . '?tab=diskusi'); }
    public function dosenResetMateri($course_id) { $materials = Material::where('course_id', $course_id)->get(); if ($materials->isEmpty()) return back()->with('error', 'Materi kosong.'); $data = $materials->map(function($m) { return ['id' => $m->id, 'judul' => $m->judul_materi]; }); $prompt = "Urutkan Materi Bab ini secara step-by-step logis. Return JSON Array of IDs saja."; $sortedIDs = $this->askGeminiJSON($prompt, $data); if ($sortedIDs && is_array($sortedIDs)) { foreach ($sortedIDs as $index => $id) { Material::where('id', $id)->update(['urutan' => $index + 1]); } return back()->with('success', 'Materi berhasil di-reset!'); } return back()->with('error', 'Gagal generate materi.'); }
    public function dosenUpdateMateri($course_id) { $existing = Material::where('course_id', $course_id)->where('urutan', '>', 0)->orderBy('urutan', 'asc')->get(['id', 'judul_materi']); $newMaterials = Material::where('course_id', $course_id)->where(function($q) { $q->where('urutan', 0)->orWhereNull('urutan'); })->get(['id', 'judul_materi', 'deskripsi_materi']); if ($newMaterials->isEmpty()) return back()->with('error', 'Tidak ada materi baru.'); foreach ($newMaterials as $newMat) { $prompt = "List Materi: " . $existing->pluck('judul_materi', 'id') . ". Baru: '{$newMat->judul_materi}'. Tentukan ID Prerequisite-nya? Return JSON: {'insert_after_id': ID} (0 jika awal)."; $result = $this->askGeminiJSON($prompt, []); $targetId = $result['insert_after_id'] ?? null; if ($targetId !== null) { $targetUrutan = 0; if ($targetId != 0) { $ref = Material::find($targetId); if ($ref) $targetUrutan = $ref->urutan; } Material::where('course_id', $course_id)->where('urutan', '>', $targetUrutan)->increment('urutan'); $newMat->urutan = $targetUrutan + 1; $newMat->save(); $existing = Material::where('course_id', $course_id)->where('urutan', '>', 0)->orderBy('urutan', 'asc')->get(); } } return back()->with('success', 'Materi baru berhasil disisipkan!'); }
}