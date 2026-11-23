<?php

namespace App\Http\Controllers;

use App\Models\Complain;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ComplainController extends Controller
{
    // Menampilkan Halaman Tabel Komplain
    public function index()
    {
        $user = Auth::user();

        if ($user->role === 'kepala_lab') {
            $complains = Complain::with(['pelapor', 'penanggap'])
                ->where('pelapor_id', $user->id)
                ->latest()
                ->get();
        } elseif (in_array($user->role, ['superadmin', 'atasan'])) {
            $complains = Complain::with(['pelapor', 'penanggap'])
                ->latest()
                ->get();
        } else {
            $complains = collect();
        }

        return view('complains.index', compact('complains'));
    }

    // Menyimpan Komplain Baru
    public function store(Request $request)
    {
        if (!in_array(Auth::user()->role, ['kepala_lab', 'superadmin'])) {
            return back()->withErrors(['Akses ditolak.']);
        }

        $request->validate([
            'nama_barang' => 'required|string|max:255',
            'deskripsi_kerusakan' => 'required|string',
            'foto_bukti' => 'required|file',
        ]);

        $fotoPath = $request->file('foto_bukti')->store('complains', 'public');

        $complain = Complain::create([
            'pelapor_id' => Auth::id(),
            'nama_barang' => $request->nama_barang,
            'deskripsi_kerusakan' => $request->deskripsi_kerusakan,
            'foto_bukti' => $fotoPath,
        ]);

        // --- NOTIFIKASI KE ATASAN/SUPERADMIN ---
        $admins = User::whereIn('role', ['atasan', 'superadmin'])
            ->where('id', '!=', auth()->id()) // <-- Ini kunci keajaibannya
            ->get();
        $url = config('waha.system_url');

        foreach ($admins as $admin) {
            if ($admin->no_wa) {
                $pesan = "*KOMPLAIN BARU MASUK*\n\n";
                $pesan .= "Halo *" . $admin->name . "*,\n";
                $pesan .= "Ada laporan kerusakan baru:\n";
                $pesan .= "*Barang:* " . $complain->nama_barang . "\n";
                $pesan .= "*Pelapor:* " . Auth::user()->name . "\n\n";
                $pesan .= "Silakan login untuk menanggapi:\n" . $url;
                $this->sendWhatsapp($admin->no_wa, $pesan);
            }
        }

        return back()->with('success', 'Komplain berhasil dikirim.');
    }

    // Menanggapi Komplain (Update Status ke Diproses)
    public function respond(Request $request, $id)
    {
        if (!in_array(Auth::user()->role, ['atasan', 'superadmin'])) {
            return back()->withErrors(['Akses ditolak.']);
        }

        $complain = Complain::findOrFail($id);

        $request->validate([
            'pesan_balasan' => 'required|string',
            'estimasi_selesai' => 'required|date',
        ]);

        $complain->update([
            'status' => 'Diproses',
            'pesan_balasan' => $request->pesan_balasan,
            'estimasi_selesai' => $request->estimasi_selesai,
            'penanggap_id' => Auth::id(),
        ]);

        // --- NOTIFIKASI BALIK KE PELAPOR ---
        if ($complain->pelapor && $complain->pelapor->no_wa) {
            $url = config('waha.system_url');
            $pesan = "*KOMPLAIN SEDANG DIPROSES*\n\n";
            $pesan .= "Halo *" . $complain->pelapor->name . "*,\n";
            $pesan .= "Komplain Anda terkait *" . $complain->nama_barang . "* telah ditanggapi.\n\n";
            $pesan .= "*Pesan:* " . $request->pesan_balasan . "\n";
            $pesan .= "*Estimasi:* " . \Carbon\Carbon::parse($request->estimasi_selesai)->format('d M Y') . "\n\n";
            $pesan .= "Cek status selengkapnya di sini:\n" . $url;
            $this->sendWhatsapp($complain->pelapor->no_wa, $pesan);
        }

        return back()->with('success', 'Tanggapan berhasil dikirim.');
    }

    // Menyelesaikan Komplain
    public function complete($id)
    {
        if (!in_array(Auth::user()->role, ['atasan', 'superadmin'])) {
            return back()->withErrors(['Akses ditolak.']);
        }

        $complain = Complain::findOrFail($id);
        $complain->update(['status' => 'Selesai']);

        // --- NOTIFIKASI SELESAI KE PELAPOR ---
        if ($complain->pelapor && $complain->pelapor->no_wa) {
            $url = config('waha.system_url');
            $pesan = "*KOMPLAIN SELESAI*\n\n";
            $pesan .= "Halo *" . $complain->pelapor->name . "*,\n";
            $pesan .= "Laporan Komplain tentang: *" . $complain->nama_barang . "* telah selesai ditangani.\n\n";
            $pesan .= "Terima kasih atas laporannya. Cek sistem:\n" . $url;
            $this->sendWhatsapp($complain->pelapor->no_wa, $pesan);
        }

        return back()->with('success', 'Status berhasil diubah menjadi Selesai.');
    }

    public function destroy($id)
    {
        try {
            $complain = Complain::findOrFail($id);
            
            // 1. FITUR KEAMANAN (Milikmu): Cegah user menghapus komplain orang lain
            if (Auth::id() != $complain->pelapor_id && Auth::user()->role !== 'superadmin') {
                // Diubah ke back() agar tidak muncul layar hitam JSON
                return back()->with('error', 'Akses ditolak! Anda tidak berhak menghapus komplain ini.');
            }

            // 2. PERSIAPAN NOTIFIKASI: Simpan nama barang sebelum dihapus
            $namaBarang = $complain->barang ?? 'Fasilitas Lab';

            // 3. CARI PENANGGAP: (Atasan & Superadmin), KECUALI si penghapus
            $penanggap = User::whereIn('role', ['atasan', 'superadmin'])
                     ->where('id', '!=', auth()->id())
                     ->get();

            // 4. KIRIM NOTIFIKASI WA PEMBATALAN
            foreach ($penanggap as $p) {
                if (!empty($p->no_wa)) {
                    $pesan = "*PEMBATALAN KOMPLAIN*\n\n";
                    $pesan .= "Halo *" . $p->name . "*,\n";
                    $pesan .= "Kami informasikan bahwa laporan komplain terkait *" . $namaBarang . "* baru saja *ditarik/dibatalkan* oleh *" . auth()->user()->name . "*.\n\n";
                    $pesan .= "Tindakan perbaikan tidak lagi diperlukan. Terima kasih!";

                    // Panggil fungsi kirim WA
                    $this->sendWhatsapp($p->no_wa, $pesan);
                }
            }

            // 5. EKSEKUSI HAPUS
            $complain->delete();
            
            return back()->with('success', 'Data komplain berhasil dihapus dan notifikasi pembatalan telah dikirim!');

        } catch (\Exception $e) {
            // Diubah ke back() agar error muncul di alert merah, bukan layar hitam JSON
            return back()->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }
}