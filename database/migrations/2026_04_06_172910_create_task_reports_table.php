<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Tabel untuk menyimpan laporan penyelesaian tugas oleh Admin/Laboran kepada Kepala Lab
        Schema::create('task_reports', function (Blueprint $table) {
                $table->id();
                
                // Relasi ke tabel tasks
                $table->foreignId('task_id')->constrained('tasks')->cascadeOnDelete();
                
                // Detail laporan
                $table->string('file_bukti')->nullable(); // Akan menyimpan path gambar/dokumen
                $table->timestamp('waktu_diselesaikan')->useCurrent();
                $table->text('argumen_terlambat')->nullable(); // Boleh kosong jika selesai tepat waktu
                
                $table->timestamps();
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_reports');
    }
};
