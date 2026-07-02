<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DaftarPoli extends Model
{
    protected $table = 'daftar_poli';

    protected $fillable = [
        'id_jadwal',
        'id_pasien',
        'keluhan',
        'no_antrian'
    ];

    /**
     * Relasi ke Pasien
     */
    public function pasien()
    {
        return $this->belongsTo(User::class, 'id_pasien');
    }

    /**
     * Relasi ke Jadwal Periksa
     */
    public function jadwalPeriksa()
    {
        return $this->belongsTo(JadwalPeriksa::class, 'id_jadwal');
    }

    /**
     * Relasi ke Periksa
     */
    public function periksas()
    {
        return $this->hasMany(Periksa::class, 'id_daftar_poli');
    }

    /**
     * Relasi ke Dokter melalui Jadwal Periksa
     */
    public function getDokter()
    {
        return $this->jadwalPeriksa->dokter ?? null;
    }

    /**
     * Relasi ke Poli melalui Dokter
     */
    public function getPoli()
    {
        return $this->getDokter()?->poli ?? null;
    }

    /**
     * Scope untuk filter berdasarkan jadwal
     */
    public function scopeByJadwal($query, $jadwalId)
    {
        return $query->where('id_jadwal', $jadwalId);
    }

    /**
     * Scope untuk filter berdasarkan pasien
     */
    public function scopeByPasien($query, $pasienId)
    {
        return $query->where('id_pasien', $pasienId);
    }

    /**
     * Scope untuk filter berdasarkan dokter
     */
    public function scopeByDokter($query, $dokterId)
    {
        return $query->whereHas('jadwalPeriksa', function($q) use ($dokterId) {
            $q->where('id_dokter', $dokterId);
        });
    }

    /**
     * Scope untuk filter yang sudah diperiksa
     */
    public function scopeSudahDiperiksa($query)
    {
        return $query->whereHas('periksas');
    }

    /**
     * Scope untuk filter yang belum diperiksa
     */
    public function scopeBelumDiperiksa($query)
    {
        return $query->whereDoesntHave('periksas');
    }

    /**
     * Cek apakah sudah diperiksa
     */
    public function sudahDiperiksa(): bool
    {
        return $this->periksas()->exists();
    }

    /**
     * Get jumlah periksa
     */
    public function getJumlahPeriksaAttribute()
    {
        return $this->periksas()->count();
    }
}

