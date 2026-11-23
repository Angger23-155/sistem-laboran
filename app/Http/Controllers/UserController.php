<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // READ: Menampilkan semua user di tabel
    public function index()
    {
        // Mengambil seluruh data dari tabel users menggunakan Eloquent ORM
        $users = User::all();
        return view('users.index', compact('users'));
    }

    // CREATE: Menampilkan antarmuka form tambah user
    public function create()
    {
        return view('users.create');
    }

    // STORE: Memvalidasi dan menyimpan user baru ke database
    public function store(Request $request)
    {
        // Lapisan Keamanan 1: Validasi Input (Backend Validation)
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users', 'regex:/@gmail\.com$/i'],
            
            // Validasi khusus WA: Hanya menerima angka dan tanda plus (+), panjang 10-20 karakter
            'no_wa' => ['nullable', 'regex:/^\+?[0-9]+$/', 'min:10', 'max:20'],
            
            'password' => 'required|string|min:8',
            'role' => 'required|in:superadmin,kepala_lab,admin,laboran,atasan',
        ], [
            // Kustomisasi pesan error agar mudah dipahami pengguna (UX)
            'email.regex' => 'Email harus menggunakan domain @gmail.com',
            'no_wa.regex' => 'Nomor WhatsApp hanya boleh berisi angka dan diawali tanda plus (+).',
        ]);

        // Lapisan Keamanan 2: Hashing Password sebelum masuk database
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'no_wa' => $request->no_wa,
            'password' => Hash::make($request->password), 
            'role' => $request->role,
        ]);

        return redirect()->route('users.index')->with('success', 'Pengguna baru berhasil ditambahkan!');
    }

    // EDIT: Menampilkan antarmuka form edit beserta data user lama
    public function edit($id)
    {
        // findOrFail berfungsi ganda: mencari data, jika ID tidak ada langsung lempar error 404
        $user = User::findOrFail($id);
        return view('users.edit', compact('user'));
    }

    // UPDATE: Memvalidasi dan menyimpan perubahan data user
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // Validasi input pembaruan data
        $request->validate([
            'name' => 'required|string|max:255',
            // Pengecualian email unik: abaikan email milik user ini sendiri agar tidak bentrok
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$user->id, 'regex:/@gmail\.com$/i'],
            
            // Validasi khusus WA yang sama dengan method store
            'no_wa' => ['nullable', 'regex:/^\+?[0-9]+$/', 'min:10', 'max:20'],
            
            'role' => 'required|in:superadmin,kepala_lab,admin,laboran,atasan',
        ], [
            'email.regex' => 'Email harus menggunakan domain @gmail.com',
            'no_wa.regex' => 'Nomor WhatsApp hanya boleh berisi angka dan diawali tanda plus (+).',
        ]);

        // Variabel penanda apakah password diubah
        $passwordChanged = false;
        $rawPassword = '';

        // Update atribut user
        $user->name = $request->name;
        $user->email = $request->email;
        $user->no_wa = $request->no_wa;
        $user->role = $request->role;

        // Logika opsional: Password hanya di-hash dan diubah JIKA form password tidak dikosongkan
        if ($request->filled('password')) {
            $request->validate(['password' => 'string|min:8']);
            $rawPassword = $request->password; // Simpan teks asli untuk dikirim via WA
            $user->password = Hash::make($request->password);
            $passwordChanged = true;
        }

        // Simpan pembaruan ke database
        $user->save();

        // --- BLOK PENGIRIMAN NOTIFIKASI WA KEAMANAN ---
        if (!empty($user->no_wa)) {
            $url = config('waha.system_url');
            
            $pesan = "🛡️ *PEMBERITAHUAN KEAMANAN AKUN*\n\n";
            $pesan .= "Halo *" . $user->name . "*,\n";
            $pesan .= "Superadmin baru saja memperbarui data akun Anda. Berikut adalah rincian informasi terbaru Anda:\n\n";
            
            $pesan .= "👤 *Nama:* " . $user->name . "\n";
            $pesan .= "📧 *Email:* " . $user->email . "\n";
            $pesan .= "📱 *WhatsApp:* " . $user->no_wa . "\n";
            $pesan .= "🏷️ *Role:* " . ucfirst($user->role) . "\n\n";

            // Jika password ikut diubah, beritahu password barunya
            if ($passwordChanged) {
                $pesan .= "🔑 *Password Baru:* " . $rawPassword . "\n";
                $pesan .= "_(Mohon segera login dan jaga kerahasiaan password ini)_\n\n";
            } else {
                $pesan .= "_(Password Anda tidak mengalami perubahan)_\n\n";
            }

            $pesan .= "Silakan akses sistem melalui tautan berikut:\n" . $url;

            // Panggil fungsi kirim WA
            $this->sendWhatsapp($user->no_wa, $pesan);
        }

        return redirect()->route('users.index')->with('success', 'Data pengguna berhasil diperbarui & Notifikasi WA terkirim!');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        
        // Fitur Keamanan: Proteksi agar Superadmin tidak bisa bunuh diri (menghapus akunnya sendiri)
        if ($user->id === auth()->id()) {
            // Jika via AJAX
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['status' => 'error', 'message' => 'Anda tidak bisa menghapus akun Anda sendiri!'], 400);
            }
            // Jika via Form Biasa
            return back()->withErrors(['Anda tidak bisa menghapus akun Anda sendiri!']);
        }

        $user->delete();

        // BALASAN KHUSUS UNTUK JAVASCRIPT (AJAX)
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Pengguna berhasil dihapus!'
            ]);
        }

        // BALASAN UNTUK FORM BIASA
        return back()->with('success', 'Pengguna berhasil dihapus!');
    }
}