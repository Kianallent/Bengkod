<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Obat extends Model
{
    protected $table = 'obat';

    protected $fillable = [
        'nama_obat',
        'kemasan',
        'harga',
        'stok',
    ];

    // Konstanta batas stok
    const STOK_HABIS = 0;
    const STOK_MENIPIS = 5;
    const STOK_NORMAL = 5;

    /**
     * Scope untuk obat dengan stok habis
     */
    public function scopeStokHabis($query)
    {
        return $query->where('stok', '<=', self::STOK_HABIS);
    }

    /**
     * Scope untuk obat dengan stok menipis (≤5)
     */
    public function scopeStokMenipis($query)
    {
        return $query->whereBetween('stok', [1, self::STOK_MENIPIS]);
    }

    /**
     * Scope untuk obat dengan stok normal (>5)
     */
    public function scopeStokNormal($query)
    {
        return $query->where('stok', '>', self::STOK_MENIPIS);
    }

    /**
     * Cek apakah stok habis
     */
    public function isStokHabis(): bool
    {
        return $this->stok <= self::STOK_HABIS;
    }

    /**
     * Cek apakah stok menipis
     */
    public function isStokMenipis(): bool
    {
        return $this->stok > self::STOK_HABIS && $this->stok <= self::STOK_MENIPIS;
    }

    /**
     * Cek apakah stok normal
     */
    public function isStokNormal(): bool
    {
        return $this->stok > self::STOK_MENIPIS;
    }

    /**
     * Get status stok dengan label
     */
    public function getStatusStok(): string
    {
        if ($this->isStokHabis()) {
            return 'Habis';
        } elseif ($this->isStokMenipis()) {
            return 'Menipis';
        } else {
            return 'Normal';
        }
    }

    public function detailPeriksas()
    {
        return $this->hasMany(DetailPeriksa::class, 'id_obat');
    }
}
