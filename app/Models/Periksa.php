<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Periksa extends Model
{
    protected $table = 'periksa';

    protected $fillable = [
        'id_daftar_poli',
        'tgl_periksa',
        'catatan',
        'biaya_periksa',
    ];

    protected $casts = [
        'tgl_periksa' => 'datetime',
    ];

    /**
     * Relasi ke DaftarPoli
     */
    public function daftarPoli()
    {
        return $this->belongsTo(DaftarPoli::class, 'id_daftar_poli');
    }

    /**
     * Relasi ke DetailPeriksa (obat yang diberikan)
     */
    public function detailPeriksas()
    {
        return $this->hasMany(DetailPeriksa::class, 'id_periksa');
    }

    /**
     * Scope untuk filter berdasarkan dokter
     */
    public function scopeByDokter($query, $dokterId)
    {
        return $query->whereHas('daftarPoli.jadwalPeriksa', function($q) use ($dokterId) {
            $q->where('id_dokter', $dokterId);
        });
    }

    /**
     * Scope untuk filter berdasarkan pasien
     */
    public function scopeByPasien($query, $pasienId)
    {
        return $query->whereHas('daftarPoli', function($q) use ($pasienId) {
            $q->where('id_pasien', $pasienId);
        });
    }

    /**
     * Scope untuk filter berdasarkan tanggal
     */
    public function scopeByTanggal($query, $tanggal)
    {
        return $query->whereDate('tgl_periksa', $tanggal);
    }

    /**
     * Scope untuk filter berdasarkan range tanggal
     */
    public function scopeByTanggalRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('tgl_periksa', [$startDate, $endDate]);
    }

    /**
     * Get jumlah obat yang diberikan
     */
    public function getJumlahObatAttribute()
    {
        return $this->detailPeriksas()->count();
    }

    /**
     * Get total biaya obat
     */
    public function getTotalBiayaObatAttribute()
    {
        return $this->detailPeriksas()
            ->with('obat')
            ->get()
            ->sum(function($detail) {
                return $detail->obat->harga;
            });
    }
}
