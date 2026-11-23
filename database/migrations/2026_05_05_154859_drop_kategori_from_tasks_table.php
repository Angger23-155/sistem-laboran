<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
   {
       Schema::table('tasks', function (Blueprint $table) {
           // Menghapus kolom 'kategori' yang usang
           $table->dropColumn('kategori');
       });
   }

   public function down()
   {
       Schema::table('tasks', function (Blueprint $table) {
           // Jika di-rollback, kembalikan kolomnya
           $table->string('kategori')->default('insidentil');
       });
   }
};
