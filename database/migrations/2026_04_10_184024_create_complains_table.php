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
        // Tabel untuk menyimpan laporan kerusakan alat yang dilaporkan oleh Kepala Lab kepada Admin/Laboran
        Schema::create('complains', function (Blueprint $table) {
            $table->id();
            
            // kepala lab yang melaporkan kerusakan
            $table->foreignId('pelapor_id')->constrained('users')->onDelete('cascade'); 
            
            // Detail Laporan
            $table->string('nama_barang');
            $table->text('deskripsi_kerusakan');
            $table->string('foto_bukti')->nullable();
            
            // Tanggapan (Atasan)
            $table->enum('status', ['Menunggu', 'Diproses', 'Selesai'])->default('Menunggu');
            $table->text('pesan_balasan')->nullable();
            $table->date('estimasi_selesai')->nullable();
            $table->foreignId('penanggap_id')->nullable()->constrained('users')->onDelete('set null'); 
            
            $table->timestamps();
        });
    }
};
