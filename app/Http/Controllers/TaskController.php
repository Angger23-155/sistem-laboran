<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use App\Models\HariLibur;
use App\Models\MasterTugasHarian;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TaskController extends Controller
{
    // Menampilkan daftar tugas berdasarkan peran pengguna
    public function index(Request $request)
    {
        $user = Auth::user();
        $hariIni = Carbon::today();

        // Ambil parameter filter tanggal dari request (jika ada)
        $filterTanggal = $request->get('tanggal_cari');

        $query = Task::with(['pemberiTugas', 'penerimaTugas']);

        if ($user->role === 'superadmin') {
            // Superadmin: Bisa lihat semua, tapi jika cari tanggal tertentu, munculkan.
            if ($filterTanggal) {
                $query->whereDate('tenggat_waktu', $filterTanggal);
            } else {
                // Default: Munculkan yang sampai hari ini saja agar tidak penuh, 
                // kecuali dia sengaja mencari tanggal masa depan.
                $query->where(function ($q) use ($hariIni) {
                    $q->where('kategori_tugas', 'insidentil')
                        ->orWhereDate('tenggat_waktu', '<=', $hariIni);
                });
            }
        } elseif ($user->role === 'kepala_lab') {
            $query->where('kepala_lab_id', $user->id);
            if ($filterTanggal) {
                $query->whereDate('tenggat_waktu', $filterTanggal);
            } else {
                $query->where(function ($q) use ($hariIni) {
                    $q->where('kategori_tugas', 'insidentil')
                        ->orWhereDate('tenggat_waktu', '<=', $hariIni);
                });
            }
        } else {
            // Laboran: TETAP TERKUNCI (Tidak bisa cari masa depan)
            $query->where('ditugaskan_ke_id', $user->id)
                ->where(function ($q) use ($hariIni) {
                    $q->where('kategori_tugas', 'insidentil')
                        ->orWhere(function ($sub) use ($hariIni) {
                            $sub->where('kategori_tugas', 'harian')
                                ->whereDate('tenggat_waktu', '<=', $hariIni);
                        });
                });
        }

        $tasks = $query->orderBy('tenggat_waktu', 'desc')->get();

        return view('tasks.index', compact('tasks'));
    }

    // Menampilkan form untuk membuat tugas baru
    public function create()
    {
        // Ambil data user yang bisa menerima tugas (Admin & Laboran)
        $users = User::whereIn('role', ['admin', 'laboran'])->get();

        // Ambil semua master tugas harian untuk dijadikan checklist
        $masters = MasterTugasHarian::all();

        return view('tasks.create', compact('users', 'masters'));
    }

    // Menyimpan tugas baru ke database dan mengirim notifikasi WA
    public function store(Request $request)
    {
        // 1. Cek Hak Akses
        if (!in_array(Auth::user()->role, ['kepala_lab', 'superadmin'])) {
            return redirect('/dashboard');
        }

        // Ambil kategori dari form, jika kosong anggap insidentil (default)
        $kategori = $request->kategori_tugas ?? 'insidentil';

        // JALUR A: TUGAS INSIDENTIL
        if ($kategori === 'insidentil') {

            $request->validate([
                'ditugaskan_ke_id' => 'required|exists:users,id',
                'jenis_pekerjaan' => 'required|string|max:255',
                'uraian_pekerjaan' => 'required|string',
                'tenggat_waktu' => 'required|date',
            ]);

            $task = Task::create([
                'kepala_lab_id' => Auth::id(),
                'ditugaskan_ke_id' => $request->ditugaskan_ke_id,
                'jenis_pekerjaan' => $request->jenis_pekerjaan,
                'uraian_pekerjaan' => $request->uraian_pekerjaan,
                'tenggat_waktu' => $request->tenggat_waktu,
                'status' => 'Pending',
                'kategori_tugas' => 'insidentil',
            ]);

            // --- NOTIFIKASI WA TUGAS INSIDENTIL ---
            if ($task->penerimaTugas && $task->penerimaTugas->no_wa) {
                $url = config('waha.system_url');
                $pesan = "*TUGAS INSIDENTIL MASUK*\n\n";
                $pesan .= "Halo *" . $task->penerimaTugas->name . "*,\n";
                $pesan .= "Anda menerima tugas baru: *" . $task->jenis_pekerjaan . "*\n";
                $pesan .= "*Deadline:* " . Carbon::parse($task->tenggat_waktu)->format('d M Y, H:i') . " WIB\n\n";
                $pesan .= "Silakan login ke sistem untuk mengerjakan:\n" . $url;

                $this->sendWhatsapp($task->penerimaTugas->no_wa, $pesan);
            }

            return redirect()->route('tasks.index')->with('success', 'Tugas Insidentil berhasil diberikan.');
        }

        // JALUR B: ROBOT GENERATOR TUGAS HARIAN
        elseif ($kategori === 'harian') {

            $request->validate([
                'ditugaskan_ke_id' => 'required|exists:users,id',
                'tahun' => 'required|numeric',
                'master_tugas' => 'required|array|min:1',
            ]);

            DB::beginTransaction();

            try {
                // 1. Rangkai uraian dari Master Tugas
                $masters = MasterTugasHarian::whereIn('id', $request->master_tugas)->get();
                $uraianGabungan = "TUGAS RUTIN HARIAN:\n";
                foreach ($masters as $m) {
                    $uraianGabungan .= "- " . $m->nama_tugas . " (" . $m->uraian_tugas . ")\n";
                }

                // 2. Siapkan Rentang Tanggal
                $tahun = $request->tahun;
                $tanggalMulai = Carbon::create($tahun, 1, 1, 0, 0, 0);
                $tanggalAkhir = Carbon::create($tahun, 12, 31, 23, 59, 59);

                // 3. Ambil Data Tanggal Merah
                $daftarLibur = HariLibur::whereYear('tanggal', $tahun)->pluck('tanggal')->toArray();
                $jumlahDibuat = 0;

                // 4. Mulai Looping Robot
                while ($tanggalMulai->lte($tanggalAkhir)) {
                    $isWeekend = $tanggalMulai->isWeekend();
                    $isHoliday = in_array($tanggalMulai->toDateString(), $daftarLibur);

                    if (!$isWeekend && !$isHoliday) {
                        Task::create([
                            'kepala_lab_id' => Auth::id(),
                            'ditugaskan_ke_id' => $request->ditugaskan_ke_id,
                            'jenis_pekerjaan' => 'Tugas Harian - ' . $tanggalMulai->translatedFormat('d F Y'),
                            'uraian_pekerjaan' => $uraianGabungan,
                            'tenggat_waktu' => $tanggalMulai->copy()->endOfDay(),
                            'status' => 'Pending',
                            'kategori_tugas' => 'harian',
                        ]);
                        $jumlahDibuat++;
                    }
                    $tanggalMulai->addDay();
                }

                // --- NOTIFIKASI REKAP TUGAS HARIAN ---
                $penerima = User::find($request->ditugaskan_ke_id);
                if ($penerima && $penerima->no_wa) {
                    $url = config('waha.system_url');
                    $pesan = "*JADWAL TUGAS HARIAN DIBUAT*\n\n";
                    $pesan .= "Halo *" . $penerima->name . "*,\n";
                    $pesan .= "Kepala Lab telah membuat jadwal otomatis sebanyak *$jumlahDibuat tugas harian* untuk Anda di tahun *$tahun*.\n\n";
                    $pesan .= "Silakan login setiap hari kerja untuk melaporkan:\n" . $url;

                    $this->sendWhatsapp($penerima->no_wa, $pesan);
                }

                DB::commit();

                return redirect()->route('tasks.index')->with('success', "Berhasil! $jumlahDibuat tugas harian telah di-generate.");

            } catch (\Exception $e) {
                DB::rollBack();
                return back()->with('error', 'Gagal men-generate tugas: ' . $e->getMessage());
            }
        }
    }

    // Menampilkan detail tugas dan laporannya
    public function show($id)
    {
        $task = Task::with(['penerimaTugas', 'report'])->findOrFail($id);

        if (
            Auth::user()->role !== 'superadmin' &&
            Auth::id() !== $task->kepala_lab_id &&
            Auth::id() !== $task->ditugaskan_ke_id
        ) {
            return redirect('/dashboard')->withErrors(['Akses ditolak.']);
        }

        return view('tasks.show', compact('task'));
    }

    // Menangani proses approval tugas oleh Kepala Lab atau Superadmin
    public function approve(Request $request, $id)
    {
        $request->validate([
            'status_approval' => 'required|in:Disetujui,Revisi',
            'catatan_revisi' => 'nullable|string'
        ]);

        $task = Task::findOrFail($id);
        $task->update([
            'status_approval' => $request->status_approval,
            'catatan_revisi' => $request->catatan_revisi
        ]);

        if ($request->status_approval == 'Revisi') {
            $task->update(['status' => 'Pending']);
            if ($task->report) {
                $task->report->delete();
            }
        }

        // --- NOTIFIKASI HASIL APPROVAL KE LABORAN ---
        if ($task->penerimaTugas && $task->penerimaTugas->no_wa) {
            $url = config('waha.system_url');
            $status = strtoupper($request->status_approval);
            $icon = ($status == 'DISETUJUI') ? "✅" : "⚠️";

            $pesan = "$icon *HASIL VALIDASI TUGAS*\n\n";
            $pesan .= "Halo *" . $task->penerimaTugas->name . "*,\n";
            $pesan .= "Tugas *" . $task->jenis_pekerjaan . "* telah dinyatakan: *$status*\n\n";

            if ($status == 'REVISI') {
                $pesan .= "*Catatan:* " . ($request->catatan_revisi ?? '-') . "\n";
                $pesan .= "Silakan login untuk memperbaiki laporan:\n" . $url;
            } else {
                $pesan .= "Terima kasih atas kerja kerasnya! Cek sistem:\n" . $url;
            }

            $this->sendWhatsapp($task->penerimaTugas->no_wa, $pesan);
        }

        return back()->with('success', 'Status approval berhasil dikirim.');
    }

    public function edit($id)
    {
        $task = Task::findOrFail($id);

        // Hanya pembuat tugas atau superadmin yang boleh edit
        if (Auth::user()->role !== 'superadmin' && Auth::id() !== $task->kepala_lab_id) {
            return redirect()->route('tasks.index')->with('error', 'Akses ditolak.');
        }

        $users = User::whereIn('role', ['admin', 'laboran'])->get();
        return view('tasks.edit', compact('task', 'users'));
    }

    public function update(Request $request, $id)
    {
        $task = Task::findOrFail($id);

        $request->validate([
            'jenis_pekerjaan' => 'required|string|max:255',
            'uraian_pekerjaan' => 'required|string',
            'tenggat_waktu' => 'required|date',
        ]);

        $task->update([
            'jenis_pekerjaan' => $request->jenis_pekerjaan,
            'uraian_pekerjaan' => $request->uraian_pekerjaan,
            'tenggat_waktu' => $request->tenggat_waktu,
        ]);

        return redirect()->route('tasks.index')->with('success', 'Tugas berhasil diperbarui.');
    }

    // Menghapus tugas
    public function destroy($id)
    {
        try {
            if (!in_array(Auth::user()->role, ['kepala_lab', 'superadmin'])) {
                return response()->json(['success' => false, 'message' => 'Akses ditolak!'], 403);
            }

            $task = Task::findOrFail($id);

            // --- NOTIFIKASI PEMBATALAN TUGAS KE PELAKSANA ---
            if ($task->status_approval != 'Disetujui' && $task->penerimaTugas && $task->penerimaTugas->no_wa) {
                $pesan = "❌ *TUGAS DIBATALKAN*\n\n";
                $pesan .= "Halo *" . $task->penerimaTugas->name . "*,\n";
                $pesan .= "Pekerjaan *" . $task->jenis_pekerjaan . "* telah **dibatalkan/ditangguhkan** oleh  " . Auth::user()->name . ".\n\n";
                $pesan .= "Anda tidak perlu lagi melanjutkan atau mengumpulkan tugas ini. Terima kasih.";

                $this->sendWhatsapp($task->penerimaTugas->no_wa, $pesan);
            }

            if ($task->report) {
                $task->report->delete();
            }
            $task->delete();

            return back()->with('success', 'Tugas berhasil dihapus!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }
   // Menghapus massal tugas harian (Fitur Darurat)
    public function bulkDelete(Request $request)
    {
        // 1. Keamanan Ekstra: Hanya Kepala Lab dan Superadmin yang boleh
        if (!in_array(Auth::user()->role, ['kepala_lab', 'superadmin'])) {
            return redirect('/dashboard')->with('error', 'Akses ditolak.');
        }

        $request->validate([
            'ditugaskan_ke_id' => 'required|exists:users,id',
            'tahun' => 'required|numeric',
        ]);

        try {
            // Ambil data pelaksana untuk mengambil nomor WA-nya nanti
            $pelaksana = User::find($request->ditugaskan_ke_id);

            // 2. Eksekusi Hapus: Kategori Harian + Tahun Target + User Target + Status Pending
            $jumlahDihapus = Task::where('kategori_tugas', 'harian')
                ->where('ditugaskan_ke_id', $request->ditugaskan_ke_id)
                ->whereYear('tenggat_waktu', $request->tahun)
                ->where('status', 'Pending')
                ->delete();

            if ($jumlahDihapus > 0) {
                
                // --- BLOK NOTIFIKASI WA PENGHAPUSAN MASSAL ---
                if ($pelaksana && !empty($pelaksana->no_wa)) {
                    $url = config('waha.system_url');

                    $pesan = "*PEMBERITAHUAN PEMBATALAN TUGAS MASSAL*\n\n";
                    $pesan .= "Halo *" . $pelaksana->name . "*,\n";
                    $pesan .= "Kami informasikan bahwa sebanyak *" . $jumlahDihapus . " Tugas Harian* Anda di tahun *" . $request->tahun . "* (yang berstatus belum dikerjakan/Pending) telah *dibersihkan* oleh " . Auth::user()->name . ".\n\n";
                    $pesan .= "Anda tidak perlu lagi mengerjakan daftar tugas tersebut. Silakan cek pembaruan jadwal Anda di sistem:\n";
                    $pesan .= $url;

                    // Panggil fungsi kirim WA
                    $this->sendWhatsapp($pelaksana->no_wa, $pesan);
                }

                return back()->with('success', "Berhasil membersihkan $jumlahDihapus tugas harian (Pending) pada tahun $request->tahun dan Notifikasi WA telah dikirim ke Pelaksana!");
            } else {
                return back()->with('info', "Tidak ada tugas harian dengan status Pending untuk dihapus pada tahun tersebut.");
            }

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus tugas: ' . $e->getMessage());
        }
    }
}