# Quick Reference - Integrasi Modul Periksa Pasien

## 🚀 Penggunaan Cepat

### 1. Ambil Data Periksa dengan Relasi Lengkap

```php
// Di Controller
$periksa = Periksa::with([
    'daftarPoli.pasien',
    'daftarPoli.jadwalPeriksa.dokter',
    'detailPeriksas.obat'
])->find($id);

// Akses data
$periksa->daftarPoli->pasien->nama;        // Nama pasien
$periksa->daftarPoli->jadwalPeriksa->dokter->nama; // Nama dokter
$periksa->detailPeriksas;                  // Obat yang diberikan
```

### 2. Filter Periksa Berdasarkan Dokter

```php
$dokterAktif = Auth::id();
$periksaDokter = Periksa::byDokter($dokterAktif)
    ->with('daftarPoli.pasien')
    ->get();
```

### 3. Filter Periksa Berdasarkan Range Tanggal

```php
$periksa = Periksa::byTanggalRange('2026-07-01', '2026-07-31')
    ->with('daftarPoli')
    ->get();
```

### 4. Cek Status Peserta Didik (Sudah/Belum Diperiksa)

```php
$daftarPoli = DaftarPoli::find($id);

if ($daftarPoli->sudahDiperiksa()) {
    // Sudah diperiksa
} else {
    // Belum diperiksa
}

// Atau gunakan scope
DaftarPoli::sudahDiperiksa()->get();  // Pasien yang sudah diperiksa
DaftarPoli::belumDiperiksa()->get();  // Pasien yang belum diperiksa
```

### 5. Akses Dokter & Poli dari DaftarPoli

```php
$daftarPoli = DaftarPoli::with('jadwalPeriksa.dokter.poli')->find($id);

$dokter = $daftarPoli->getDokter();      // User dokter
$poli = $daftarPoli->getPoli();          // Poli

// Atau via jadwal
$jadwal = $daftarPoli->jadwalPeriksa;
$dokter = $jadwal->dokter;
$poli = $dokter->poli;
```

### 6. Cek User Role

```php
if (auth()->user()->isDokter()) {
    // Tampilkan dashboard dokter
}

if (auth()->user()->isPasien()) {
    // Tampilkan dashboard pasien
}

// Atau gunakan scope
User::dokter()->get();   // Semua dokter
User::pasien()->get();   // Semua pasien
User::admin()->get();    // Semua admin
```

### 7. Hitung Statistik Pasien Periksa

```php
// Di PeriksaPasienController
$daftarPasien = DaftarPoli::with([...])
    ->whereHas('jadwalPeriksa', fn($q) => $q->where('id_dokter', Auth::id()))
    ->get();

$totalPasien = $daftarPasien->count();
$sudahDiperiksa = $daftarPasien->filter(fn($d) => $d->sudahDiperiksa())->count();
$belumDiperiksa = $totalPasien - $sudahDiperiksa;
```

### 8. Tampilkan Informasi Obat di Periksa

```php
$periksa = Periksa::with('detailPeriksas.obat')->find($id);

foreach ($periksa->detailPeriksas as $detail) {
    echo $detail->obat->nama_obat;  // Nama obat
    echo $detail->obat->harga;      // Harga
    echo $detail->obat->stok;       // Stok saat ini
}
```

### 9. Cek Status Stok Obat

```php
$obat = Obat::find($id);

if ($obat->isStokHabis()) {
    // Obat habis (stok = 0)
}

if ($obat->isStokMenipis()) {
    // Obat menipis (stok 1-5)
}

if ($obat->isStokNormal()) {
    // Obat normal (stok > 5)
}

// Atau gunakan scope
Obat::stokHabis()->get();    // Obat habis
Obat::stokMenipis()->get();  // Obat menipis
Obat::stokNormal()->get();   // Obat normal
```

### 10. Hitung Jumlah Obat & Biaya Periksa

```php
$periksa = Periksa::with('detailPeriksas.obat')->find($id);

$jumlahObat = $periksa->jumlah_obat;         // Accessor
$totalBiayaObat = $periksa->total_biaya_obat; // Accessor
$totalBiaya = $periksa->biaya_periksa + $totalBiayaObat;
```

### 11. Ambil Jadwal Periksa Dokter

```php
$dokter = User::find($dokterId);
$jadwal = $dokter->jadwalPeriksa()
    ->with('daftarPolis.pasien')
    ->get();
```

### 12. Hitung Pasien Terperiksa per Jadwal

```php
$jadwal = JadwalPeriksa::find($id);

$totalPasien = $jadwal->jumlah_pasien;              // Accessor
$terperiksa = $jadwal->getJumlahTerperiksa();      // Method
$belumTerperiksa = $jadwal->getJumlahBelumTerperiksa();
```

## 📊 Relasi Diagram

```
┌─────────────────────────────────────────────────────────────┐
│ User (Dokter)                                               │
│  ├── jadwalPeriksa  (1:M)                                   │
│  ├── periksas       (hasManyThrough)                        │
│  └── poli           (1:1 belongsTo)                         │
└─────────────────────────────────────────────────────────────┘
           ↓
┌─────────────────────────────────────────────────────────────┐
│ JadwalPeriksa                                               │
│  ├── dokter         (belongsTo)                             │
│  ├── daftarPolis    (1:M)                                   │
│  └── poli           (hasManyThrough via dokter)             │
└─────────────────────────────────────────────────────────────┘
           ↓
┌─────────────────────────────────────────────────────────────┐
│ DaftarPoli                                                  │
│  ├── pasien         (belongsTo User)                        │
│  ├── jadwalPeriksa  (belongsTo)                             │
│  ├── periksas       (1:M)                                   │
│  ├── dokter         (getDokter method)                      │
│  └── poli           (getPoli method)                        │
└─────────────────────────────────────────────────────────────┘
           ↓
┌─────────────────────────────────────────────────────────────┐
│ Periksa                                                     │
│  ├── daftarPoli     (belongsTo)                             │
│  └── detailPeriksas (1:M)                                   │
└─────────────────────────────────────────────────────────────┘
           ↓
┌─────────────────────────────────────────────────────────────┐
│ DetailPeriksa (Obat yang diberikan)                         │
│  ├── periksa        (belongsTo)                             │
│  └── obat           (belongsTo)                             │
└─────────────────────────────────────────────────────────────┘
           ↓
┌─────────────────────────────────────────────────────────────┐
│ Obat                                                        │
│  └── detailPeriksas (1:M)                                   │
└─────────────────────────────────────────────────────────────┘
```

## 🎯 Common Queries

```php
// Ambil semua periksa bulan ini dengan dokter dan pasien
$periksa = Periksa::byTanggalRange(
    now()->startOfMonth(),
    now()->endOfMonth()
)->with([
    'daftarPoli.pasien',
    'daftarPoli.jadwalPeriksa.dokter',
    'detailPeriksas.obat'
])->get();

// Ambil pasien yang belum diperiksa untuk dokter spesifik
$belumDiperiksa = DaftarPoli::byDokter($dokterId)
    ->belumDiperiksa()
    ->with('pasien', 'jadwalPeriksa')
    ->orderBy('no_antrian')
    ->get();

// Ambil statistik obat yang sering diberikan
$obatSering = DetailPeriksa::selectRaw('id_obat, COUNT(*) as total')
    ->groupBy('id_obat')
    ->orderByDesc('total')
    ->with('obat')
    ->limit(10)
    ->get();

// Hitung total biaya periksa per dokter
$biayaPerDokter = User::dokter()
    ->with('periksas')
    ->get()
    ->map(function($dokter) {
        return [
            'dokter' => $dokter->nama,
            'total_periksa' => $dokter->periksas->count(),
            'total_biaya' => $dokter->periksas->sum('biaya_periksa')
        ];
    });
```

---
**💡 Tips**: Selalu gunakan eager loading saat mengakses relasi untuk menghindari N+1 query problem.
