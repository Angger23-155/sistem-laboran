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
        // Tabel untuk menyimpan tugas yang diberikan oleh Kepala Lab kepada Admin/Laboran
        Schema::create('tasks', function (Blueprint $table) {
                $table->id();
                
                // Relasi ke tabel users
                $table->foreignId('kepala_lab_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('ditugaskan_ke_id')->constrained('users')->cascadeOnDelete();
                
                // Detail pekerjaan
                $table->string('jenis_pekerjaan');
                $table->text('uraian_pekerjaan');
                $table->dateTime('tenggat_waktu');
                $table->enum('status', ['Pending', 'Selesai Tepat Waktu', 'Selesai Terlambat'])->default('Pending');
                
                $table->timestamps();
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
