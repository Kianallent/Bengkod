<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Poli extends Model
{
    protected $table = 'poli';

    protected $fillable = [
        'nama_poli',
        'keterangan',
    ];

    /**
     * Relasi ke Dokter
     */
    public function dokters()
    {
        return $this->hasMany(User::class, 'id_poli');
    }

    /**
     * Get jumlah dokter di poli ini
     */
    public function getJumlahDokterAttribute(): int
    {
        return $this->dokters()->count();
    }

    /**
     * Get jumlah jadwal periksa
     */
    public function getJumlahJadwalAttribute(): int
    {
        return $this->dokters()
            ->with('jadwalPeriksa')
            ->get()
            ->sum(function($dokter) {
                return $dokter->jadwalPeriksa()->count();
            });
    }
}
