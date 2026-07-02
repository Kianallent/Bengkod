<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JadwalPeriksa extends Model
{
    protected $table = 'jadwal_periksa';

    protected $fillable = [
        'id_dokter',
        'hari',
        'jam_mulai',
        'jam_selesai',
    ];

    /**
     * Relasi ke Dokter
     */
    public function dokter()
    {
        return $this->belongsTo(User::class, 'id_dokter');
    }

    /**
     * Relasi ke Daftar Poli
     */
    public function daftarPolis()
    {
        return $this->hasMany(DaftarPoli::class, 'id_jadwal');
    }

    /**
     * Relasi ke Poli melalui Dokter
     */
    public function poli()
    {
        return $this->hasManyThrough(
            Poli::class,
            User::class,
            'id',
            'id',
            'id_dokter',
            'id_poli'
        );
    }

    /**
     * Scope untuk filter jadwal aktif
     */
    public function scopeAktif($query)
    {
        return $query->where('aktif', 1);
    }

    /**
     * Scope untuk filter berdasarkan dokter
     */
    public function scopeByDokter($query, $dokterId)
    {
        return $query->where('id_dokter', $dokterId);
    }

    /**
     * Scope untuk filter berdasarkan hari
     */
    public function scopeByHari($query, $hari)
    {
        return $query->where('hari', $hari);
    }

    /**
     * Get nama hari dalam bahasa Indonesia
     */
    public function getNamaHariAttribute(): string
    {
        $hariMap = [
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu',
            'Sunday' => 'Minggu',
        ];
        
        return $hariMap[$this->hari] ?? $this->hari;
    }

    /**
     * Get jumlah pasien dalam jadwal ini
     */
    public function getJumlahPasienAttribute(): int
    {
        return $this->daftarPolis()->count();
    }

    /**
     * Get jumlah pasien yang sudah diperiksa
     */
    public function getJumlahTerperiksa()
    {
        return $this->daftarPolis()
            ->whereHas('periksas')
            ->count();
    }

    /**
     * Get jumlah pasien yang belum diperiksa
     */
    public function getJumlahBelumTerperiksa()
    {
        return $this->daftarPolis()
            ->whereDoesntHave('periksas')
            ->count();
    }
}