<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $guarded = ['id']; // Mengizinkan pengisian data selain ID

    // Relasi ke pemberi tugas (Kepala Lab)
    public function pemberiTugas()
    {
        return $this->belongsTo(User::class, 'kepala_lab_id');
    }

    // Relasi ke penerima tugas (Admin/Laboran)
    public function penerimaTugas()
    {
        return $this->belongsTo(User::class, 'ditugaskan_ke_id');
    }
    // Relasi ke laporan tugas
    // Relasi ke tabel laporan (Satu tugas punya satu laporan)
    public function report()
    {
        return $this->hasOne(TaskReport::class);
    }
}