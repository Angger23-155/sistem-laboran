<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB; // Jangan lupa tambahkan ini di atas

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Menyuruh MySQL untuk memperbarui daftar ENUM dengan menambahkan 'atasan'
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('superadmin', 'kepala_lab', 'admin', 'laboran', 'atasan') NOT NULL DEFAULT 'laboran'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Mengembalikan ke kondisi semula jika di-rollback
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('superadmin', 'kepala_lab', 'admin', 'laboran') NOT NULL DEFAULT 'laboran'");
    }
};