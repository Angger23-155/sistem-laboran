<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskReport extends Model
{
    use HasFactory;

    // Mengizinkan pengisian semua kolom kecuali ID
    protected $guarded = ['id']; 

    // Relasi balik ke tabel Tasks
    public function task()
    {
        return $this->belongsTo(Task::class);
    }
}