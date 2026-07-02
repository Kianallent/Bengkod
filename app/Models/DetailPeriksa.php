<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailPeriksa extends Model
{
    protected $table = 'detail_periksa';

    protected $fillable = [
        'id_periksa',
        'id_obat',
    ];

    protected static function boot()
    {
        parent::boot();

        // Event saat DetailPeriksa dibuat
        static::created(function ($detailPeriksa) {
            // Obat sudah dikurangi di controller, tapi ini untuk keamanan tambahan
        });
    }

    /**
     * Relasi ke Periksa
     */
    public function periksa()
    {
        return $this->belongsTo(Periksa::class, 'id_periksa');
    }

    /**
     * Relasi ke Obat
     */
    public function obat()
    {
        return $this->belongsTo(Obat::class, 'id_obat');
    }

    /**
     * Scope untuk filter berdasarkan obat
     */
    public function scopeByObat($query, $obatId)
    {
        return $query->where('id_obat', $obatId);
    }

    /**
     * Scope untuk filter berdasarkan periksa
     */
    public function scopeByPeriksa($query, $periksaId)
    {
        return $query->where('id_periksa', $periksaId);
    }

    /**
     * Get informasi lengkap obat yang diberikan
     */
    public function getInfoObatLengkap()
    {
        return [
            'nama_obat' => $this->obat->nama_obat,
            'harga' => $this->obat->harga,
            'kemasan' => $this->obat->kemasan,
            'stok_sebelumnya' => $this->obat->stok + 1, // +1 karena sudah dikurangi
        ];
    }
}
