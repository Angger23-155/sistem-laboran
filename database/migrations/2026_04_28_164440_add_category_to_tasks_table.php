<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->enum('kategori', ['insidentil', 'harian'])->default('insidentil')->after('id');
            // Kolom ini untuk menandai tugas harian ini untuk tanggal berapa
            $table->date('tanggal_tugas')->nullable()->after('tenggat_waktu');
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
