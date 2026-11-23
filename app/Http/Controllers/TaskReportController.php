<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\TaskReport;

class TaskReportController extends Controller
{
    public function store(Request $request, $taskId)
    {
        // Ambil data task beserta relasi pemberi tugas agar no_wa bisa dibaca
        $task = Task::with('pemberiTugas')->findOrFail($taskId);
        
        // Simpan status sebelum diupdate untuk mengecek apakah ini laporan revisi
        $isRevision = ($task->status_approval === 'Revisi');

        // Cek apakah laporan ini terlambat
        $isLate = now() > $task->tenggat_waktu;
        
        // BLOKIR JIKA TUGAS HARIAN SUDAH LEWAT HARI
        if ($task->kategori_tugas === 'harian' && $isLate) {
            return back()->with('error', 'Tugas Harian sudah kedaluwarsa (Hangus) dan tidak dapat dilaporkan pada hari berikutnya.');
        }

        // Validasi: Jika terlambat, argumen WAJIB diisi. Jika tugas harian, checklist WAJIB diisi.
        $request->validate([
            'file_bukti' => 'required|file|mimes:jpg,png,pdf,docx|max:2048',
            'argumen_terlambat' => $isLate ? 'required|string' : 'nullable',
            'checklist_harian' => ($task->kategori_tugas === 'harian') ? 'required|array|min:1' : 'nullable',
        ]);

        // Proses Checklist (Jika ada) menjadi teks rapi untuk dikirim via WA
        $teksChecklist = "";
        if ($request->has('checklist_harian')) {
            $teksChecklist = "*DETAIL PEKERJAAN SELESAI:*\n";
            foreach ($request->checklist_harian as $item) {
                $teksChecklist .= "- " . $item . "\n";
            }
            $teksChecklist .= "\n";
        }

        // --- PROSES SIMPAN FILE FISIK DENGAN PENGECEKAN AMAN ---
        if ($request->hasFile('file_bukti') && $request->file('file_bukti')->isValid()) {
            $file = $request->file('file_bukti');
            // Menghilangkan spasi pada nama file untuk mencegah error di beberapa server
            $fileName = time() . '_' . str_replace(' ', '_', $file->getClientOriginalName()); 
            $file->storeAs('public/bukti_laporan', $fileName);
        } else {
            // Mengembalikan user ke form dengan pesan error yang jelas
            return back()->with('error', 'Gagal memproses file! Pastikan ukuran file di bawah 2MB dan formatnya sesuai (JPG/PNG/PDF/DOCX).');
        }

        // Simpan data laporan ke database
        TaskReport::create([
            'task_id' => $task->id,
            'file_bukti' => $fileName,
            'waktu_diselesaikan' => now(),
            'argumen_terlambat' => $isLate ? $request->argumen_terlambat : null,
        ]);

        // Update status tugas (JALUR PINTAS TUGAS HARIAN)
        $status = $isLate ? 'Selesai Terlambat' : 'Selesai Tepat Waktu';
        $task->update([
            'status' => $status,
            // Perbaikan: Ubah null menjadi 'Disetujui' untuk tugas harian agar database tidak error
            'status_approval' => ($task->kategori_tugas === 'harian') ? 'Disetujui' : 'Menunggu'
        ]);

        // --- NOTIFIKASI LAPORAN MASUK KE PEMBERI TUGAS ---
        if ($task->pemberiTugas && $task->pemberiTugas->no_wa) {
            $url = config('waha.system_url');
            
            if ($isRevision) {
                $pesan = "*PERBAIKAN TUGAS DIKIRIM*\n\n";
                $pesan .= "Halo *" . $task->pemberiTugas->name . "*,\n";
                $pesan .= "*" . auth()->user()->name . "* telah mengirimkan **REVISI** untuk tugas: *" . $task->jenis_pekerjaan . "*\n\n";
            } else {
                $pesan = "*LAPORAN TUGAS MASUK*\n\n";
                $pesan .= "Halo *" . $task->pemberiTugas->name . "*,\n";
                $pesan .= "*" . auth()->user()->name . "* telah menyelesaikan tugas: *" . $task->jenis_pekerjaan . "*\n\n";
            }

            // Tambahkan detail checklist ke pesan WA (jika ada isinya)
            if (!empty($teksChecklist)) {
                $pesan .= $teksChecklist;
            }

            // Jika terlambat, tambahkan argumen keterlambatan
            if ($isLate && $request->filled('argumen_terlambat')) {
                $pesan .= "*Status: Terlambat*\n";
                $pesan .= "*Alasan:* " . $request->argumen_terlambat . "\n\n";
            }

            // Kalimat penutup WA dibedakan
            if ($task->kategori_tugas === 'harian') {
                $pesan .= "Laporan harian ini dapat dilihat pada sistem:\n" . $url;
            } else {
                $pesan .= "Mohon segera diperiksa dan divalidasi di sistem:\n" . $url;
            }

            $this->sendWhatsapp($task->pemberiTugas->no_wa, $pesan);
        }

        return back()->with('success', 'Laporan berhasil dikirim dan Notifikasi WA telah diteruskan ke Pemberi Tugas!');
    }
}