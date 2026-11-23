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
       Schema::table('master_tugas_harians', function (Blueprint $table) {
           // Menambah kolom enum setelah uraian_tugas
           $table->enum('role_tujuan', ['admin', 'laboran'])->default('laboran')->after('uraian_tugas');
       });
   }

   public function down()
   {
       Schema::table('master_tugas_harians', function (Blueprint $table) {
           $table->dropColumn('role_tujuan');
       });
   }
};
