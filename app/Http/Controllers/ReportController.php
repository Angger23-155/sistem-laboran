<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ReportController extends Controller
{
    // Menampilkan halaman form filter laporan
    public function index()
    {
        $user = Auth::user();
        
        // Atasan (misal Kaprodi/Dekan) tidak bisa akses halaman ini
        if ($user->role === 'atasan') {
            return redirect('/dashboard')->with('error', 'Akses ditolak.');
        }

        $laborans = [];
        // Jika yang login Superadmin/Kepala Lab, ambil data laboran untuk pilihan dropdown
        if (in_array($user->role, ['superadmin', 'kepala_lab'])) {
            $laborans = User::whereIn('role', ['admin', 'laboran'])->get();
        }

        return view('reports.index', compact('laborans'));
    }

    // Memproses data dan mencetak PDF
    public function cetakPdf(Request $request)
    {
        // 1. Tambahkan validasi untuk input 'kolom'
        $request->validate([
            'tgl_awal' => 'required|date',
            'tgl_akhir' => 'required|date|after_or_equal:tgl_awal',
            'kolom' => 'required|array|min:1', // Memastikan minimal ada 1 kolom yang dicentang
        ]);

        $user = Auth::user();
        
        // 2. Simpan pilihan kolom ke dalam variabel
        $kolom = $request->kolom;
        
        // Tarik data tugas berdasarkan rentang tanggal
        $query = Task::with(['penerimaTugas', 'pemberiTugas'])
                     ->whereBetween('tenggat_waktu', [$request->tgl_awal . ' 00:00:00', $request->tgl_akhir . ' 23:59:59']);

        // Logika pembatasan data
        if (in_array($user->role, ['superadmin', 'kepala_lab'])) {
            // Kepala Lab bisa pilih cetak laboran spesifik atau semuanya
            if ($request->user_id) {
                $query->where('ditugaskan_ke_id', $request->user_id);
                $targetUser = User::find($request->user_id)->name;
            } else {
                $targetUser = "Semua Laboran & Admin";
            }
        } else {
            // Laboran HANYA BISA cetak laporannya sendiri
            $query->where('ditugaskan_ke_id', $user->id);
            $targetUser = $user->name;
        }

        // Eksekusi pengambilan data
        $tasks = $query->orderBy('tenggat_waktu', 'asc')->get();

        // ============================================================
        // LOGIKA PENILAIAN DAN FILTER STATUS
        // ============================================================
        $skorDiperoleh = 0;
        $tugasDinilai = 0;

        foreach ($tasks as $task) {
            // 1. Filter Status Waktu (Harian vs Insidentil)
            if ($task->status === 'Pending' && Carbon::parse($task->tenggat_waktu)->isPast()) {
                if (strtolower($task->kategori_tugas) == 'harian') {
                    $task->status = 'Tidak Terlaksana';
                    // --- PERBAIKAN BUG ---
                    // Ubah status persetujuan menjadi strip karena tidak ada yang perlu disetujui
                    $task->status_approval = '-'; 
                } else {
                    $task->status = 'Pending (Lewat Deadline)';
                }
            }

            // 2. Mesin Penghitung Skor
            // Tugas 'Pending' yang batas waktunya masih aman TIDAK masuk hitungan evaluasi
            if ($task->status !== 'Pending') {
                $tugasDinilai++; // Menambah penyebut (jumlah tugas yang dievaluasi)

                if ($task->status === 'Selesai Tepat Waktu') {
                    $skorDiperoleh += 100;
                } elseif ($task->status === 'Selesai Terlambat') {
                    $skorDiperoleh += 50;
                } elseif (in_array($task->status, ['Revisi', 'Direvisi'])) {
                    $skorDiperoleh += 75; 
                }
                // Status lain seperti Hangus/Tidak Terlaksana/Pending Telat mendapat +0
            }
        }

        // 3. Kalkulasi Persentase Akhir
        $nilaiKinerja = $tugasDinilai > 0 ? round($skorDiperoleh / $tugasDinilai, 1) : 0;
        // ============================================================

        // Rangkuman Data
        $summary = [
            'total' => $tasks->count(),
            'tepat_waktu' => $tasks->where('status', 'Selesai Tepat Waktu')->count(),
            'terlambat' => $tasks->where('status', 'Selesai Terlambat')->count(),
            'revisi' => $tasks->whereIn('status', ['Revisi', 'Direvisi'])->count(),
            'tidak_terlaksana' => $tasks->where('status', 'Tidak Terlaksana')->count(),
            'pending_telat' => $tasks->where('status', 'Pending (Lewat Deadline)')->count(),
            'pending_aman' => $tasks->where('status', 'Pending')->count(),
        ];

        // 4. Tambahkan $nilaiKinerja dan $tugasDinilai ke compact
        $pdf = Pdf::loadView('reports.pdf', compact('tasks', 'summary', 'request', 'targetUser', 'kolom', 'nilaiKinerja', 'tugasDinilai'));
        
        // Tampilkan PDF di tab baru browser
        return $pdf->stream('Laporan_Kinerja_' . date('Y-m-d') . '.pdf');
    }
}