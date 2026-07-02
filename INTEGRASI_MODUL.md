# Dokumentasi Integrasi Modul Periksa Pasien

## 📊 Integrasi yang Telah Dilakukan

### 1. **Model Enhancements** 

#### Model Periksa ([app/Models/Periksa.php](app/Models/Periksa.php))
- ✅ Relasi ke DaftarPoli (many-to-one)
- ✅ Relasi ke DetailPeriksa (one-to-many untuk obat)
- ✅ Scope `byDokter()` - filter periksa berdasarkan dokter
- ✅ Scope `byPasien()` - filter periksa berdasarkan pasien
- ✅ Scope `byTanggal()` & `byTanggalRange()` - filter by date
- ✅ Accessor `getJumlahObatAttribute()` - total obat diberikan
- ✅ Accessor `getTotalBiayaObatAttribute()` - total biaya obat

#### Model DaftarPoli ([app/Models/DaftarPoli.php](app/Models/DaftarPoli.php))
- ✅ Method `getDokter()` - akses dokter via jadwal
- ✅ Method `getPoli()` - akses poli via dokter
- ✅ Scope `byJadwal()`, `byPasien()`, `byDokter()`
- ✅ Scope `sudahDiperiksa()` & `belumDiperiksa()` - filter status
- ✅ Method `sudahDiperiksa()` - cek apakah sudah diperiksa
- ✅ Accessor `getJumlahPeriksaAttribute()` - total periksa

#### Model JadwalPeriksa ([app/Models/JadwalPeriksa.php](app/Models/JadwalPeriksa.php))
- ✅ Relasi ke Poli via Dokter (hasManyThrough)
- ✅ Accessor `getNamaHariAttribute()` - nama hari indo
- ✅ Accessor `getJumlahPasienAttribute()` - total pasien terdaftar
- ✅ Method `getJumlahTerperiksa()` - pasien yang sudah diperiksa
- ✅ Method `getJumlahBelumTerperiksa()` - pasien yang belum diperiksa

#### Model User ([app/Models/User.php](app/Models/User.php))
- ✅ Relasi `daftarPolis()` - pasien yang terdaftar
- ✅ Relasi `periksas()` (hasManyThrough) - periksa yang dilakukan dokter
- ✅ Scope `dokter()`, `pasien()`, `admin()` - filter by role
- ✅ Method `isDokter()`, `isPasien()`, `isAdmin()` - cek role
- ✅ Accessor `getJumlahJadwalhariAttribute()` - total jadwal dokter
- ✅ Accessor `getJumlahPasienTerdaftarAttribute()` - total pasien terdaftar untuk pasien

#### Model DetailPeriksa ([app/Models/DetailPeriksa.php](app/Models/DetailPeriksa.php))
- ✅ Scope `byObat()` & `byPeriksa()` - filter data
- ✅ Method `getInfoObatLengkap()` - info obat dengan stok sebelumnya

#### Model Poli ([app/Models/Poli.php](app/Models/Poli.php))
- ✅ Accessor `getJumlahDokterAttribute()` - total dokter
- ✅ Accessor `getJumlahJadwalAttribute()` - total jadwal periksa

### 2. **Controller Enhancements**

#### PeriksaPasienController ([app/Http/Controllers/Dokter/PeriksaPasienController.php](app/Http/Controllers/Dokter/PeriksaPasienController.php))
- ✅ Eager loading relasi dengan select specific columns
- ✅ Menghitung statistik: total pasien, sudah/belum diperiksa
- ✅ Order by no_antrian
- ✅ Integration dengan Jadwal Periksa, Pasien, Poli

#### ObatController ([app/Http/Controllers/Admin/ObatController.php](app/Http/Controllers/Admin/ObatController.php))
- ✅ Order by stok ascending (menampilkan stok terbatas di atas)
- ✅ Hitung obat stok menipis & habis
- ✅ Pass statistik ke view

### 3. **View Enhancements**

#### Periksa Pasien Index ([resources/views/dokter/periksa-pasien/index.blade.php](resources/views/dokter/periksa-pasien/index.blade.php))
- ✅ Statistik cards (Total, Sudah, Belum Diperiksa)
- ✅ Tampilkan No. RM pasien
- ✅ Tampilkan No. HP pasien
- ✅ Highlight baris untuk pasien yang sudah diperiksa
- ✅ Status badge (Diperiksa/Menunggu)
- ✅ Limit text keluhan dengan Str::limit()
- ✅ Responsive layout (grid & table)

#### Periksa Pasien Create ([resources/views/dokter/periksa-pasien/create.blade.php](resources/views/dokter/periksa-pasien/create.blade.php))
- ✅ Alert notifikasi stok menipis
- ✅ Display stok status per obat (Habis/Menipis/Normal)
- ✅ Badge indikator di list terpilih
- ✅ Validasi error display
- ✅ Component reusable alert-errors

#### Admin Obat Index ([resources/views/admin/obat/index.blade.php](resources/views/admin/obat/index.blade.php))
- ✅ Alert statistik stok habis & menipis
- ✅ Kolom Status dengan badge warna
- ✅ Highlight baris untuk stok terbatas
- ✅ Sorting otomatis by stok

## 🔗 Relasi Data Flow

```
Periksa
  ├── DaftarPoli
  │   ├── Pasien (User)
  │   ├── JadwalPeriksa
  │   │   ├── Dokter (User)
  │   │   │   └── Poli
  │   │   └── DaftarPoli
  │   └── Periksas
  └── DetailPeriksa
      ├── Periksa
      └── Obat
          ├── Stok Management
          └── DetailPeriksas
```

## 📋 Query Optimization

### Eager Loading Pattern
```php
$daftarPasien = DaftarPoli::with([
    'pasien:id,nama,no_rm,no_hp,alamat',
    'jadwalPeriksa:id,id_dokter,hari,jam_mulai,jam_selesai',
    'jadwalPeriksa.dokter:id,nama,id_poli',
    'jadwalPeriksa.dokter.poli:id,nama_poli',
    'periksas:id,id_daftar_poli,tgl_periksa,catatan,biaya_periksa',
    'periksas.detailPeriksas:id,id_periksa,id_obat'
])->whereHas(...)->get();
```

## ✨ Fitur yang Diintegrasikan

1. **CRUD Obat Integration**
   - Validasi stok otomatis
   - Pengurangan stok real-time
   - Indikator stok menipis di form dan admin

2. **Jadwal Periksa Integration**
   - Filter pasien by dokter via jadwal
   - Menampilkan jadwal info di list pasien
   - Hitung statistik pasien per jadwal

3. **Pasien Integration**
   - Tampilkan detail pasien (nama, No. RM, No. HP)
   - Akses riwayat periksa pasien
   - Validation dengan id daftar poli

4. **Poli Integration**
   - Display poli info di pasien
   - Count dokter & jadwal per poli
   - Filter by poli (untuk admin dashboard)

5. **Statistik & Report**
   - Total pasien per dokter
   - Pasien sudah/belum diperiksa
   - Total obat diberikan
   - Biaya periksa dengan breakdown

## 🎯 Penggunaan Scope di Aplikasi

```php
// Filter by dokter
Periksa::byDokter($dokterId)->get();

// Filter by pasien
Periksa::byPasien($pasienId)->get();

// Filter by tanggal
Periksa::byTanggal('2026-07-01')->get();

// Filter by range tanggal
Periksa::byTanggalRange($start, $end)->get();

// Pasien sudah/belum diperiksa
DaftarPoli::sudahDiperiksa()->get();
DaftarPoli::belumDiperiksa()->get();

// Dokter/Pasien/Admin
User::dokter()->get();
User::pasien()->get();
User::admin()->get();

// Obat by stok status
Obat::stokHabis()->get();
Obat::stokMenipis()->get();
Obat::stokNormal()->get();
```

## 📈 Next Steps (Opsional)

1. **Report & Analytics**
   - Dashboard statistik periksa
   - Laporan obat yang sering diberikan
   - Laporan biaya periksa per periode

2. **Advanced Filtering**
   - Filter by Poli
   - Filter by Tanggal Range
   - Export to PDF/Excel

3. **Notification System**
   - Notif stok obat habis
   - Reminder jadwal periksa
   - Alert periksa menunggu

4. **Mobile Optimization**
   - Responsive design untuk mobile
   - Quick action buttons di home

---
**Status**: ✅ Integrasi Lengkap  
**Last Updated**: 1 Juli 2026
