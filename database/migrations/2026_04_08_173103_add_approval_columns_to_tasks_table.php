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
        // Menambahkan kolom status_approval dan catatan_revisi pada tabel tasks untuk menyimpan informasi persetujuan tugas oleh Kepala Lab
        Schema::table('tasks', function (Blueprint $table) {
            $table->enum('status_approval', ['Menunggu', 'Disetujui', 'Revisi'])->default('Menunggu')->after('status');
            $table->text('catatan_revisi')->nullable()->after('status_approval');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            //
        });
    }
};
