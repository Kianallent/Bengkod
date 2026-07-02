<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]

   class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'nama',
        'alamat',
        'no_ktp',
        'no_hp',
        'no_rm',
        'role',
        'id_poli',
        'email',
        'password',

    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function poli()
    {
        return $this->belongsTo(Poli::class, 'id_poli');
    }

    public function jadwalPeriksa()
    {
        return $this->hasMany(JadwalPeriksa::class, 'id_dokter');
    }

    /**
     * Relasi untuk pasien (daftar poli yang mereka buat)
     */
    public function daftarPolis()
    {
        return $this->hasMany(DaftarPoli::class, 'id_pasien');
    }

    /**
     * Relasi untuk periksa yang dilakukan dokter
     */
    public function periksas()
    {
        return $this->hasManyThrough(
            Periksa::class,
            JadwalPeriksa::class,
            'id_dokter',
            'id_daftar_poli',
            'id',
            'id'
        );
    }

    /**
     * Scope untuk filter dokter
     */
    public function scopeDokter($query)
    {
        return $query->where('role', 'dokter');
    }

    /**
     * Scope untuk filter pasien
     */
    public function scopePasien($query)
    {
        return $query->where('role', 'pasien');
    }

    /**
     * Scope untuk filter admin
     */
    public function scopeAdmin($query)
    {
        return $query->where('role', 'admin');
    }

    /**
     * Cek apakah user adalah dokter
     */
    public function isDokter(): bool
    {
        return $this->role === 'dokter';
    }

    /**
     * Cek apakah user adalah pasien
     */
    public function isPasien(): bool
    {
        return $this->role === 'pasien';
    }

    /**
     * Cek apakah user adalah admin
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Get jumlah jadwal periksa
     */
    public function getJumlahJadwalhariAttribute(): int
    {
        return $this->jadwalPeriksa()->count();
    }

    /**
     * Get jumlah pasien yang terdaftar
     */
    public function getJumlahPasienTerdaftarAttribute(): int
    {
        return $this->daftarPolis()->count();
    }

}

