<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Complain extends Model
{
    use HasFactory;

    protected $fillable = [
        'pelapor_id', 'nama_barang', 'deskripsi_kerusakan', 'foto_bukti',
        'status', 'pesan_balasan', 'estimasi_selesai', 'penanggap_id'
    ];

    // Relasi ke tabel User (Pelapor)
    public function pelapor()
    {
        return $this->belongsTo(User::class, 'pelapor_id');
    }

    // Relasi ke tabel User (Atasan yang menanggapi)
    public function penanggap()
    {
        return $this->belongsTo(User::class, 'penanggap_id');
    }
}