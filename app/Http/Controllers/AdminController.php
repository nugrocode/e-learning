<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Services\GeminiService;
use App\Models\User;
use App\Models\Concentration;
use App\Models\Course;
use App\Models\Announcement;
use App\Models\Material;

class AdminController extends Controller
{
    protected GeminiService $gemini;

    public function __construct(GeminiService $geminiService)
    {
        $this->gemini = $geminiService;
    }

    public function dashboard() {
        $total_mhs = User::where('role', 'mahasiswa')->count();
        $total_dosen = User::where('role', 'dosen')->count();
        $total_konsentrasi = Concentration::count();
        $total_mk = Course::count();
        
        $recent_users = User::latest()->take(5)->get();

        $chart_users = [
            'mahasiswa' => $total_mhs,
            'dosen' => $total_dosen,
            'admin' => User::where('role', 'admin')->count()
        ];

        $months = [];
        $mhs_trend = [];
        $dosen_trend = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::today()->startOfMonth()->subMonths($i);
            $months[] = $date->translatedFormat('M Y');

            $mhs_trend[] = User::where('role', 'mahasiswa')
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)->count();

            $dosen_trend[] = User::where('role', 'dosen')
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)->count();
        }
        $chart_trend = [
            'labels' => $months,
            'mahasiswa' => $mhs_trend,
            'dosen' => $dosen_trend
        ];

        $concentrations = Concentration::withCount('courses')->get();
        $chart_prodi = [
            'labels' => $concentrations->pluck('nama_konsentrasi')->toArray(),
            'data' => $concentrations->pluck('courses_count')->toArray()
        ];

        $video_count = Material::where('kategori', 'video')->whereNull('tipe_submission')->count();
        $tugas_count = Material::whereNotNull('tipe_submission')->count();
        $quiz_count = Material::where('kategori', 'quiz')->count();

        $chart_materi = [
            'video' => $video_count,
            'tugas' => $tugas_count,
            'kuis' => $quiz_count
        ];

        return view('admin.dashboard', compact(
            'total_mhs', 'total_dosen', 'total_konsentrasi', 'total_mk', 'recent_users',
            'chart_users', 'chart_trend', 'chart_prodi', 'chart_materi'
        ));
    }

    public function usersIndex(Request $request) {
        if (!$request->has('role')) {
            return redirect('/admin/users?role=mahasiswa');
        }

        $query = User::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_lengkap', 'LIKE', "%{$search}%")
                  ->orWhere('nim_nidn', 'LIKE', "%{$search}%");
            });
        }

        $query->where('role', $request->role);
        $users = $query->latest()->paginate(10)->withQueryString();

        $stats = [
            'total' => User::count(),
            'dosen' => User::where('role', 'dosen')->count(),
            'mahasiswa' => User::where('role', 'mahasiswa')->count(),
            'admin' => User::where('role', 'admin')->count(),
        ];

        return view('admin.users', compact('users', 'stats'));
    }

    public function userStore(Request $request) {
        $request->validate([
            'nim' => 'required|unique:users,nim_nidn',
            'nama_lengkap' => 'required',
            'role' => 'required',
            'password' => 'required|min:4',
        ]);

        $filename = null;
        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('profiles', $filename, 'public');
        }

        User::create([
            'nim_nidn' => $request->nim,
            'nama_lengkap' => $request->nama_lengkap,
            'password' => md5($request->password), 
            'role' => $request->role,
            'foto_profil' => $filename
        ]);

        return back()->with('success', 'Pengguna baru berhasil ditambahkan!');
    }

    public function userUpdate(Request $request, string $id) {
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
            if ($user->foto_profil && $user->foto_profil != 'default.png') {
                Storage::disk('public')->delete('profiles/' . $user->foto_profil);
            }

            $file = $request->file('foto');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('profiles', $filename, 'public');
            $user->foto_profil = $filename;
        }

        $user->nim_nidn = $request->nim;
        $user->nama_lengkap = $request->nama_lengkap;
        $user->role = $request->role;
        $user->save();

        if ($id == Session::get('user_id')) {
            Session::put('nama', $user->nama_lengkap);
            if ($user->foto_profil) {
                Session::put('foto', $user->foto_profil);
            }
        }

        return back()->with('success', 'Data pengguna diperbarui!');
    }

    public function userDestroy(string $id) {
        if ($id == Session::get('user_id')) {
            return back()->with('error', 'Anda tidak dapat menghapus akun sendiri!');
        }

        $user = User::findOrFail($id);

        DB::table('progress')->where('user_id', $id)->delete();
        DB::table('submissions')->where('user_id', $id)->delete();
        DB::table('quiz_scores')->where('user_id', $id)->delete();
        DB::table('discussions')->where('user_id', $id)->delete();
        DB::table('notifications')->where('user_id', $id)->orWhere('sender_id', $id)->delete();
        Course::where('dosen_id', $id)->update(['dosen_id' => null]);

        if ($user->foto_profil && $user->foto_profil != 'default.png') {
            Storage::disk('public')->delete('profiles/' . $user->foto_profil);
        }
        
        $user->delete();
        return back()->with('success', 'Pengguna berhasil dihapus.');
    }

    public function pengumumanIndex() { 
        $pengumuman = Announcement::latest()->get(); 
        return view('admin.pengumuman', compact('pengumuman')); 
    }

    public function pengumumanStore(Request $request) { 
        Announcement::create($request->all() + ['is_active' => 1]); 
        return back()->with('success', 'Pengumuman diterbitkan!'); 
    }

    public function pengumumanUpdate(Request $request, string $id) { 
        Announcement::findOrFail($id)->update($request->all()); 
        return back()->with('success', 'Pengumuman diperbarui!'); 
    }

    public function pengumumanDestroy(string $id) { 
        Announcement::findOrFail($id)->delete(); 
        return back()->with('success', 'Pengumuman dihapus.'); 
    }

    public function konsentrasiIndex() {
        $konsentrasi = Concentration::all();
        foreach($konsentrasi as $k) { 
            $k->total_mk = $k->courses()->count(); 
        }
        return view('admin.konsentrasi', compact('konsentrasi'));
    }
    
    public function konsentrasiStore(Request $request) {
        $filename = null;
        if ($request->hasFile('gambar')) { 
            $file = $request->file('gambar'); 
            $filename = time() . '_' . $file->getClientOriginalName(); 
            $file->storeAs('thumbnails', $filename, 'public');
        }
        Concentration::create([
            'nama_konsentrasi' => $request->nama_konsentrasi, 
            'deskripsi' => $request->deskripsi, 
            'gambar' => $filename
        ]);
        return back()->with('success', 'Prodi ditambahkan!');
    }
    
    public function konsentrasiUpdate(Request $request, string $id) {
        $k = Concentration::findOrFail($id);
        if ($request->hasFile('gambar')) { 
            if ($k->gambar) Storage::disk('public')->delete('thumbnails/' . $k->gambar);
            $file = $request->file('gambar'); 
            $filename = time() . '_' . $file->getClientOriginalName(); 
            $file->storeAs('thumbnails', $filename, 'public');
            $k->gambar = $filename; 
        }
        $k->update($request->except('gambar'));
        return back()->with('success', 'Data diperbarui!');
    }
    
    public function konsentrasiDestroy(string $id) { 
        $k = Concentration::findOrFail($id);
        if ($k->gambar) Storage::disk('public')->delete('thumbnails/' . $k->gambar);
        $k->delete(); 
        return back()->with('success', 'Prodi dihapus.'); 
    }

    public function bankIndex() {
        $courses = Course::with('dosen')->latest()->get();
        $dosens = User::where('role', 'dosen')->get();
        return view('admin.bank_mk', compact('courses', 'dosens'));
    }

    public function bankStore(Request $request) {
        $request->validate([
            'nama_mk' => 'required|unique:courses,nama_mk',
            'dosen_id' => 'required',
        ]);

        $filename = null;
        if ($request->hasFile('gambar')) {
            $file = $request->file('gambar');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('thumbnails', $filename, 'public');
        }

        Course::create([
            'nama_mk' => $request->nama_mk,
            'dosen_id' => $request->dosen_id,
            'deskripsi' => $request->deskripsi,
            'gambar' => $filename
        ]);

        return back()->with('success', 'Mata Kuliah baru ditambahkan ke Bank Data.');
    }

    public function bankUpdate(Request $request, string $id) {
        $c = Course::findOrFail($id);
        $request->validate([
            'nama_mk' => 'required|unique:courses,nama_mk,'.$id,
            'dosen_id' => 'required',
        ]);

        if ($request->hasFile('gambar')) {
            if ($c->gambar) Storage::disk('public')->delete('thumbnails/' . $c->gambar);
            
            $file = $request->file('gambar');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('thumbnails', $filename, 'public');
            $c->gambar = $filename;
        }

        $c->update($request->except('gambar'));
        return back()->with('success', 'Data Master Mata Kuliah diperbarui.');
    }

    public function bankDestroy(string $id) {
        $c = Course::findOrFail($id);
        if ($c->gambar) Storage::disk('public')->delete('thumbnails/' . $c->gambar);
        
        $c->concentrations()->detach();
        $c->delete(); 
        return back()->with('success', 'Mata Kuliah dihapus permanen.');
    }

    public function kurikulumIndex() {
        $konsentrasi = Concentration::all();
        foreach($konsentrasi as $k) { $k->total_mk = $k->courses()->count(); }
        return view('admin.kurikulum', compact('konsentrasi'));
    }

    public function kurikulumShow(string $id) {
        $konsentrasi = Concentration::findOrFail($id);
        $dosens = User::where('role', 'dosen')->get();

        $all_courses = $konsentrasi->courses()->with('dosen')->get();

        $courses = $all_courses->filter(function($c) {
            return $c->pivot->urutan > 0;
        })->sortBy('pivot.urutan')->values();

        $new_courses = $all_courses->filter(function($c) {
            return $c->pivot->urutan == 0 || is_null($c->pivot->urutan);
        })->values();

        return view('admin.kurikulum_detail', compact('konsentrasi', 'courses', 'new_courses', 'dosens'));
    }

    public function courseStore(Request $request) {
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
                $file->storeAs('thumbnails', $filename, 'public');
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
            return back()->with('success', 'Mata Kuliah ditambahkan! Klik "Smart Insert" untuk menyisipkannya.');
        }

        return back()->with('error', 'Mata Kuliah sudah ada di kurikulum ini.');
    }

    public function courseUpdate(Request $request, string $id) {
        return $this->bankUpdate($request, $id);
    }

    public function courseDestroy(string $id) {
        $concentration_id = request('concentration_id'); 
        
        if($concentration_id) {
            $konsentrasi = Concentration::find($concentration_id);
            $konsentrasi->courses()->detach($id); 
            return back()->with('success', 'Mata Kuliah dihapus dari kurikulum ini.');
        }
        return back()->with('error', 'Gagal menghapus.');
    }

    
    public function autoDistribute() {
        set_time_limit(300); 
        $concentrations = Concentration::all();
        $courses = Course::all(); 

        if ($courses->isEmpty()) return back()->with('error', 'Belum ada data Mata Kuliah.');

        $listKonsentrasi = $concentrations->map(function($c) { return "ID:{$c->id}={$c->nama_konsentrasi}"; })->implode(", ");
        $listCourses = $courses->map(function($c) { return "ID:{$c->id}={$c->nama_mk}(" . Str::limit($c->deskripsi, 50) . ")"; })->implode("\n");

        $prompt = "
            Anda adalah Dekan Ilmu Komputer tingkat Master yang jenius dan sangat teliti.
            Tugas Anda: Distribusikan daftar Mata Kuliah ke dalam Konsentrasi/Prodi secara AKURAT, KETAT, dan SPESIFIK.

            LIST KONSENTRASI: [{$listKonsentrasi}]
            LIST MATA KULIAH: 
            {$listCourses}
            
            ATURAN SANGAT KETAT: 
            1. FUNDAMENTAL (Wajib di SEMUA ID konsentrasi): HANYA mata kuliah dasar absolut seperti Dasar Pemrograman, Struktur Data, Basis Data, OOP, dan Jaringan Komputer.
            2. SPESIFIK (SANGAT EKSKLUSIF): Mata kuliah tingkat lanjut/kejuruan HARUS didistribusikan ke HANYA SATU ID konsentrasi yang paling relevan. JANGAN PERNAH MENCAMPURNYA!
               - Contoh: Web, Software, SQA, Microservices, Cloud -> HANYA ke Software Development.
               - Contoh: Kecerdasan Buatan, Machine Learning, Data Mining, Preprocessing -> HANYA ke Machine Learning.
               - Contoh: Mikrokontroler, IoT, Embedded System -> HANYA ke Internet of Things.
            
            WAJIB BALAS DENGAN JSON OBJECT STRICT SEPERTI BERIKUT:
            {
              \"data\": [
                 {\"course_id\": 1, \"target_concentration_ids\":},
                 {\"course_id\": 2, \"target_concentration_ids\":}
              ]
            }
        ";

        $response = $this->gemini->ask($prompt, true);
        
        $mappings = $response['data'] ?? $response;

        if (is_array($mappings) && count($mappings) > 0) {
            
            DB::table('concentration_course')->delete();

            $count = 0;
            foreach ($mappings as $map) {
                if(isset($map['course_id']) && isset($map['target_concentration_ids']) && is_array($map['target_concentration_ids'])){
                    foreach ($map['target_concentration_ids'] as $concId) {
                        DB::table('concentration_course')->insert([
                            'concentration_id' => $concId, 
                            'course_id' => $map['course_id'], 
                            'urutan' => 0
                        ]);
                        $count++;
                    }
                }
            }
            return back()->with('success', "Sempurna! AI telah membersihkan kurikulum lama dan merancang ulang {$count} penempatan mata kuliah secara akurat.");
        }
        return back()->with('error', 'AI gagal merespons dengan format yang valid. Silakan klik tombol sekali lagi.');
    }

   
    public function resetKurikulum(string $concentration_id) {
        set_time_limit(300); 
        
        $konsentrasi = Concentration::findOrFail($concentration_id);
        $courses = $konsentrasi->courses()->get();

        if ($courses->isEmpty()) return back()->with('error', 'Data kosong.');

        $data = $courses->map(function($c) { return "ID:{$c->id} | Nama: {$c->nama_mk}"; })->implode("\n");
        
        $prompt = "
            Anda adalah Pakar Kurikulum Akademik IT.
            Tugas Anda: Mengurutkan daftar Mata Kuliah berikut untuk membentuk Learning Path dari tingkat paling DASAR (Semester 1) hingga tingkat paling AHLI/MAHIR (Semester Akhir).

            DATA MATA KULIAH YANG HARUS DIURUTKAN:
            {$data}

            WAJIB BALAS DENGAN JSON OBJECT STRICT SEPERTI BERIKUT:
            {
                \"sorted_ids\":
            }
        ";
        
        $response = $this->gemini->ask($prompt, true);
        $sortedIDs = $response['sorted_ids'] ?? $response;

        if ($sortedIDs && is_array($sortedIDs) && count($sortedIDs) === $courses->count()) {
            foreach ($sortedIDs as $index => $course_id) { 
                $konsentrasi->courses()->updateExistingPivot($course_id, ['urutan' => $index + 1]);
            }
            return back()->with('success', 'Luar biasa! AI berhasil menyusun ulang roadmap kurikulum secara rapi dari level dasar hingga ahli.');
        }
        
        return back()->with('error', 'AI gagal memproses urutan atau ada data yang tertinggal. Silakan coba klik lagi.');
    }

    public function updateKurikulum(string $concentration_id) {
        set_time_limit(300);
        $konsentrasi = Concentration::findOrFail($concentration_id);
        
        $pending = $konsentrasi->courses()->wherePivot('urutan', 0)->get();

        if ($pending->isEmpty()) {
            return back()->with('error', 'Tidak ada mata kuliah baru yang perlu disisipkan.');
        }

        $berhasil = 0;
        $gagal = 0;

        foreach ($pending as $newCourse) {
            $existing = $konsentrasi->courses()
                ->wherePivot('urutan', '>', 0)
                ->orderBy('concentration_course.urutan', 'asc')
                ->get();

            if ($existing->isEmpty()) {
                $konsentrasi->courses()->updateExistingPivot($newCourse->id, ['urutan' => 1]);
                $berhasil++;
                continue;
            }

            $listExisting = $existing->map(function($c) { 
                return "ID: {$c->id} | Nama: {$c->nama_mk}"; 
            })->implode("\n");

            $prompt = "
                Anda adalah Pakar Kurikulum IT.
                Berikut adalah urutan kurikulum saat ini dari tingkat dasar hingga akhir:
                {$listExisting}

                Terdapat Mata Kuliah Baru yang belum memiliki posisi: '{$newCourse->nama_mk}'
                
                Tugas Anda HANYA SATU: Tentukan posisi (ID mata kuliah) yang paling tepat SEBELUM mata kuliah baru ini.
                Aturan Mutlak:
                1. Jika mata kuliah baru ini adalah materi sangat dasar dan harus berada di paling awal, wajib jawab dengan angka 0.
                2. Anda HANYA boleh memilih ID yang ada pada daftar di atas. Jangan mengarang ID.
                3. Berikan jawaban murni dalam format JSON.
                
                Balas HANYA dengan format JSON seperti ini:
                {\"insert_after_id\": <angka_id>}
            ";
            
            $result = $this->gemini->ask($prompt, true);
            
            $targetId = null;

            if (is_array($result) && array_key_exists('insert_after_id', $result) && is_numeric($result['insert_after_id'])) {
                $targetId = (int) $result['insert_after_id'];
            } 
            
            if ($targetId === null) {
                $gagal++;
                continue;
            }

            $targetUrutan = 0;
            
            if ($targetId !== 0) {
                $ref = DB::table('concentration_course')
                    ->where('concentration_id', $concentration_id)
                    ->where('course_id', $targetId)
                    ->first();
                    
                if ($ref) {
                    $targetUrutan = $ref->urutan;
                } else {
                    $targetUrutan = $existing->max('pivot.urutan') ?? 0;
                }
            }
            
            DB::table('concentration_course')
                ->where('concentration_id', $concentration_id)
                ->where('urutan', '>', $targetUrutan)
                ->increment('urutan');
            
            $konsentrasi->courses()->updateExistingPivot($newCourse->id, ['urutan' => $targetUrutan + 1]);
            $berhasil++;
        }
        
        $pesan = "Proses selesai! {$berhasil} Mata Kuliah berhasil disisipkan 100% oleh AI.";
        if ($gagal > 0) {
            $pesan .= " Namun, AI tidak dapat menentukan posisi untuk {$gagal} Mata Kuliah, silakan klik tombol sekali lagi.";
        }
        
        return back()->with('success', $pesan);
    }
}