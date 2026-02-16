<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Services\GeminiService; // [Inject Service AI]

// Import Models
use App\Models\User;
use App\Models\Concentration;
use App\Models\Course;
use App\Models\Announcement;

class AdminController extends Controller
{
    protected $gemini;

    /**
     * Inject GeminiService untuk fitur AI Admin
     */
    public function __construct(GeminiService $geminiService)
    {
        $this->gemini = $geminiService;
    }

    // ==========================================================
    // 1. DASHBOARD & UTAMA
    // ==========================================================

    public function dashboard() {
        $total_mhs = User::where('role', 'mahasiswa')->count();
        $total_dosen = User::where('role', 'dosen')->count();
        $total_konsentrasi = Concentration::count();
        $total_mk = Course::count();
        
        $recent_users = User::latest()->take(5)->get();
        
        return view('admin.dashboard', compact('total_mhs', 'total_dosen', 'total_konsentrasi', 'total_mk', 'recent_users'));
    }


    // ==========================================================
    // 2. MANAJEMEN PENGGUNA (USERS)
    // ==========================================================

    public function usersIndex(Request $request) {
        // Default redirect jika tidak ada role
        if (!$request->has('role')) {
            return redirect('/admin/users?role=mahasiswa');
        }

        $query = User::query();

        // Fitur Pencarian
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_lengkap', 'LIKE', "%{$search}%")
                  ->orWhere('nim_nidn', 'LIKE', "%{$search}%");
            });
        }

        $query->where('role', $request->role);
        $users = $query->latest()->paginate(10)->withQueryString();

        // Statistik Header
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
            'password' => md5($request->password), // MD5 Sesuai legacy code
            'role' => $request->role,
            'foto_profil' => $filename
        ]);

        return back()->with('success', 'Pengguna baru berhasil ditambahkan!');
    }

    public function userUpdate(Request $request, $id) {
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

        return back()->with('success', 'Data pengguna diperbarui!');
    }

    public function userDestroy($id) {
        if ($id == Session::get('user_id')) {
            return back()->with('error', 'Anda tidak dapat menghapus akun sendiri!');
        }

        $user = User::findOrFail($id);

        // Hapus Data Terkait (Clean Up)
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


    // ==========================================================
    // 3. MANAJEMEN PENGUMUMAN
    // ==========================================================

    public function pengumumanIndex() { 
        $pengumuman = Announcement::latest()->get(); 
        return view('admin.pengumuman', compact('pengumuman')); 
    }

    public function pengumumanStore(Request $request) { 
        Announcement::create($request->all() + ['is_active' => 1]); 
        return back()->with('success', 'Pengumuman diterbitkan!'); 
    }

    public function pengumumanUpdate(Request $request, $id) { 
        Announcement::findOrFail($id)->update($request->all()); 
        return back()->with('success', 'Pengumuman diperbarui!'); 
    }

    public function pengumumanDestroy($id) { 
        Announcement::findOrFail($id)->delete(); 
        return back()->with('success', 'Pengumuman dihapus.'); 
    }


    // ==========================================================
    // 4. MASTER DATA (PRODI & BANK MK)
    // ==========================================================

    // --- KONSENTRASI / PRODI ---
    public function konsentrasiIndex() {
        $konsentrasi = Concentration::all();
        // Hitung total MK
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
    
    public function konsentrasiUpdate(Request $request, $id) {
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
    
    public function konsentrasiDestroy($id) { 
        $k = Concentration::findOrFail($id);
        if ($k->gambar) Storage::disk('public')->delete('thumbnails/' . $k->gambar);
        $k->delete(); 
        return back()->with('success', 'Prodi dihapus.'); 
    }

    // --- BANK MATA KULIAH ---
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

    public function bankUpdate(Request $request, $id) {
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

    public function bankDestroy($id) {
        $c = Course::findOrFail($id);
        if ($c->gambar) Storage::disk('public')->delete('thumbnails/' . $c->gambar);
        
        $c->concentrations()->detach(); // Lepas hubungan dengan prodi
        $c->delete(); 
        return back()->with('success', 'Mata Kuliah dihapus permanen.');
    }


    // ==========================================================
    // 5. MANAJEMEN KURIKULUM & DISTRIBUSI (AI POWERED)
    // ==========================================================

    public function kurikulumIndex() {
        $konsentrasi = Concentration::all();
        foreach($konsentrasi as $k) { $k->total_mk = $k->courses()->count(); }
        return view('admin.kurikulum', compact('konsentrasi'));
    }

    public function kurikulumShow($id) {
        $konsentrasi = Concentration::findOrFail($id);
        $dosens = User::where('role', 'dosen')->get();

        // MK yang sudah diurutkan (Semester 1 dst)
        $courses = $konsentrasi->courses()
            ->with('dosen') 
            ->wherePivot('urutan', '>', 0)
            ->orderBy('concentration_course.urutan', 'asc') 
            ->get();
        
        // MK yang baru masuk (Pending Sort)
        $new_courses = $konsentrasi->courses()
            ->wherePivot('urutan', 0)
            ->get();

        return view('admin.kurikulum_detail', compact('konsentrasi', 'courses', 'new_courses', 'dosens'));
    }

    public function courseStore(Request $request) {
        $request->validate([
            'concentration_id' => 'required',
            'nama_mk' => 'required',
        ]);

        // Cek apakah MK sudah ada di Bank Data
        $course = Course::where('nama_mk', $request->nama_mk)->first();

        // Jika belum ada, buat baru di Bank Data
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

        // Cek relasi agar tidak duplikat di prodi yang sama
        $exists = DB::table('concentration_course')
            ->where('concentration_id', $request->concentration_id)
            ->where('course_id', $course->id)
            ->exists();
        
        if (!$exists) {
            $konsentrasi = Concentration::find($request->concentration_id);
            $konsentrasi->courses()->attach($course->id, ['urutan' => 0]); // Urutan 0 = Pending
            return back()->with('success', 'Mata Kuliah ditambahkan! Klik "Smart Insert" untuk menyisipkannya.');
        }

        return back()->with('error', 'Mata Kuliah sudah ada di kurikulum ini.');
    }

    public function courseUpdate(Request $request, $id) {
        // Wrapper untuk update MK (sama seperti di Bank MK)
        return $this->bankUpdate($request, $id);
    }

    public function courseDestroy($id) {
        $concentration_id = request('concentration_id'); 
        
        if($concentration_id) {
            $konsentrasi = Concentration::find($concentration_id);
            $konsentrasi->courses()->detach($id); 
            return back()->with('success', 'Mata Kuliah dihapus dari kurikulum ini.');
        }
        return back()->with('error', 'Gagal menghapus.');
    }

    // --- FITUR AI: AUTO DISTRIBUTE ---
    public function autoDistribute() {
        $concentrations = Concentration::all();
        $courses = Course::all(); 

        if ($courses->isEmpty()) return back()->with('error', 'Belum ada data Mata Kuliah.');

        // Format data untuk Prompt AI
        $listKonsentrasi = $concentrations->map(function($c) { return "ID:{$c->id}={$c->nama_konsentrasi}"; })->implode(", ");
        $listCourses = $courses->map(function($c) { return "ID:{$c->id}={$c->nama_mk}(" . Str::limit($c->deskripsi, 50) . ")"; })->implode("\n");

        $prompt = "
            Role: Kaprodi. Tugas: Distribusikan Mata Kuliah ke Prodi yang sesuai.
            LIST PRODI: [{$listKonsentrasi}]
            LIST MK: 
            {$listCourses}
            
            ATURAN: 
            1. MK Umum (Agama, Bahasa, Pancasila, Coding Dasar) -> Masukkan ke SEMUA ID Prodi.
            2. MK Spesifik -> Hanya ke ID Prodi yang relevan (Misal: 'Jaringan' ke Teknik Komputer).
            
            OUTPUT: JSON Array valid saja: [{\"course_id\": 1, \"target_concentration_ids\": [1, 2]}]
        ";

        // Panggil Service Gemini (Mode JSON)
        $mappings = $this->gemini->ask($prompt, true);

        if (is_array($mappings)) {
            $count = 0;
            foreach ($mappings as $map) {
                foreach ($map['target_concentration_ids'] as $concId) {
                    // Cek duplikasi sebelum insert
                    $exists = DB::table('concentration_course')
                        ->where('concentration_id', $concId)
                        ->where('course_id', $map['course_id'])
                        ->exists();

                    if (!$exists) {
                        DB::table('concentration_course')->insert([
                            'concentration_id' => $concId, 
                            'course_id' => $map['course_id'], 
                            'urutan' => 0
                        ]);
                        $count++;
                    }
                }
            }
            return back()->with('success', "AI berhasil mendistribusikan {$count} mata kuliah!");
        }
        return back()->with('error', 'AI tidak memberikan respons valid.');
    }

    // --- FITUR AI: RESET & SORT ---
    public function resetKurikulum($concentration_id) {
        $konsentrasi = Concentration::findOrFail($concentration_id);
        $courses = $konsentrasi->courses()->get();

        if ($courses->isEmpty()) return back()->with('error', 'Data kosong.');

        $data = $courses->map(function($c) { return "ID:{$c->id}, Nama:{$c->nama_mk}"; })->implode("\n");
        $prompt = "Urutkan mata kuliah berikut secara logis dari Semester 1 (Dasar) sampai Akhir (Lanjutan/Skripsi). \nDATA:\n{$data}\nOUTPUT: JSON Array of IDs only: [1, 5, 2...]";
        
        $sortedIDs = $this->gemini->ask($prompt, true);

        if ($sortedIDs && is_array($sortedIDs)) {
            foreach ($sortedIDs as $index => $course_id) { 
                $konsentrasi->courses()->updateExistingPivot($course_id, ['urutan' => $index + 1]);
            }
            return back()->with('success', 'Kurikulum berhasil disusun ulang oleh AI!');
        }
        return back()->with('error', 'Gagal memproses AI.');
    }

    // --- FITUR AI: SMART INSERT ---
    public function updateKurikulum($concentration_id) {
        $konsentrasi = Concentration::findOrFail($concentration_id);
        
        // Ambil MK yang sudah urut
        $existing = $konsentrasi->courses()->wherePivot('urutan', '>', 0)->orderBy('pivot_urutan', 'asc')->get();
        // Ambil MK baru (urutan 0)
        $pending = $konsentrasi->courses()->wherePivot('urutan', 0)->get();

        if ($pending->isEmpty()) return back()->with('error', 'Tidak ada MK baru yang perlu disisipkan.');

        foreach ($pending as $newCourse) {
            $prompt = "List MK Kurikulum saat ini (Urut): " . $existing->pluck('nama_mk', 'id') . ". \nMK Baru yang mau masuk: {$newCourse->nama_mk}. \nDi posisi mana (setelah ID berapa) MK Baru ini harus disisipkan agar kurikulum tetap logis? Jawab JSON: {'insert_after_id': ID_TARGET} (Jawab 0 jika harus di awal).";
            
            $result = $this->gemini->ask($prompt, true);
            $targetId = $result['insert_after_id'] ?? null;

            if ($targetId !== null) {
                $targetUrutan = 0;
                
                // Cari urutan target
                if ($targetId != 0) {
                    $ref = DB::table('concentration_course')
                        ->where('concentration_id', $concentration_id)
                        ->where('course_id', $targetId)
                        ->first();
                    if ($ref) $targetUrutan = $ref->urutan;
                }
                
                // Geser semua urutan setelah target +1
                DB::table('concentration_course')
                    ->where('concentration_id', $concentration_id)
                    ->where('urutan', '>', $targetUrutan)
                    ->increment('urutan');
                
                // Masukkan MK baru di slot yang kosong
                $konsentrasi->courses()->updateExistingPivot($newCourse->id, ['urutan' => $targetUrutan + 1]);
                
                // Refresh list existing untuk iterasi berikutnya
                $existing = $konsentrasi->courses()->wherePivot('urutan', '>', 0)->orderBy('pivot_urutan', 'asc')->get();
            }
        }
        return back()->with('success', 'Mata Kuliah baru berhasil disisipkan secara cerdas!');
    }
}