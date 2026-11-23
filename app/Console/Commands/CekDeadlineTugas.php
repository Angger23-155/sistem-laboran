<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class CekDeadlineTugas extends Command
{
    // Nama perintah yang akan dipanggil di terminal
    protected $signature = 'tugas:cek-deadline';

    protected $description = 'Mengecek deadline tugas dan mengirimkan notifikasi WA peringatan';

    public function handle()
    {
        // Ambil semua tugas yang statusnya masih 'Pending'
        $tasks = Task::with('penerimaTugas')->where('status', 'Pending')->get();
        $now = Carbon::now();
        $url = config('waha.system_url');

        foreach ($tasks as $task) {
            // Abaikan jika tidak ada data penerima atau tidak punya nomor WA
            if (!$task->penerimaTugas || !$task->penerimaTugas->no_wa) continue;

            $deadline = Carbon::parse($task->tenggat_waktu);
            
            $diffHours = $now->diffInHours($deadline, false);

            // Peringatan Sisa Waktu menuju deadline
            if ($diffHours <= 5 && $diffHours > 4) {
                $pesan = "⏳ *PERINGATAN DEADLINE*\n\n";
                $pesan .= "Halo *" . $task->penerimaTugas->name . "*,\n";
                $pesan .= "Waktu pengerjaan tugas *" . $task->jenis_pekerjaan . "* tersisa **kurang dari 5 Jam**.\n\n";
                $pesan .= "⏰ *Tenggat:* " . $deadline->format('d M Y, H:i') . " WIB\n\n";
                $pesan .= "Segera selesaikan dan kumpulkan melalui sistem:\n" . $url;
                
                $this->kirimWa($task->penerimaTugas->no_wa, $pesan);
                $this->info("Kirim peringatan 5 jam ke " . $task->penerimaTugas->name);
            }

            // Peringatan Tugas Telat (Dikirim saat baru telat 0 sampai 1 jam)
            if ($diffHours < 0 && $diffHours >= -1) {
                $pesan = "🚨 *TUGAS TERLAMBAT!*\n\n";
                $pesan .= "Halo *" . $task->penerimaTugas->name . "*,\n";
                $pesan .= "Waktu pengerjaan tugas *" . $task->jenis_pekerjaan . "* **TELAH HABIS**.\n\n";
                $pesan .= "Anda wajib segera login ke sistem, mengumpulkan tugas, dan **memberikan alasan/argumen keterlambatan**.\n\n";
                $pesan .= "Login di sini:\n" . $url;
                
                $this->kirimWa($task->penerimaTugas->no_wa, $pesan);
                $this->info("Kirim peringatan telat ke " . $task->penerimaTugas->name);
            }
        }

        $this->info('Pengecekan deadline selesai dijalankan.');
    }

    // Fungsi internal untuk kirim WA (Karena file ini bukan Controller, di buat fungsinya sendiri)
    private function kirimWa($target, $message)
    {
        $target = str_replace(['+', ' ', '-'], '', $target);
        if (str_starts_with($target, '08')) {
            $target = '628' . substr($target, 2);
        }
        if (!str_contains($target, '@c.us')) {
            $target .= '@c.us';
        }

        try {
            $wahaUrl = config('waha.url');
            $apiKey = config('waha.api_key');
            $session = config('waha.session');

            Http::withHeaders([
                'X-Api-Key' => $apiKey,
                'Content-Type' => 'application/json',
            ])->post($wahaUrl . '/api/sendText', [
                'session' => $session,
                'chatId' => $target,
                'text' => $message,
            ]);
        } catch (\Exception $e) {
        }
    }
}