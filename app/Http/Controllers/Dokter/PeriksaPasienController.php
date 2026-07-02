<?php

namespace App\Http\Controllers\Dokter;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePeriksaPasienRequest;
use App\Models\DaftarPoli;
use App\Models\DetailPeriksa;
use App\Models\Obat;
use App\Models\Periksa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PeriksaPasienController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // ini ambil id dokter dari user yang sedang login 1.
        $dokterId = Auth::id();

        // Ambil data dengan eager loading untuk performa optimal
        $daftarPasien = DaftarPoli::with([
                'pasien:id,nama,no_rm,no_hp,alamat',
                'jadwalPeriksa:id,id_dokter,hari,jam_mulai,jam_selesai',
                'jadwalPeriksa.dokter:id,nama,id_poli',
                'jadwalPeriksa.dokter.poli:id,nama_poli',
                'periksas:id,id_daftar_poli,tgl_periksa,catatan,biaya_periksa',
                'periksas.detailPeriksas:id,id_periksa,id_obat'
            ])
            ->whereHas('jadwalPeriksa', function ($query) use ($dokterId) {
                $query->where('id_dokter', $dokterId);
            })
            ->orderBy('no_antrian')
            ->get();

        // Hitung statistik
        $totalPasien = $daftarPasien->count();
        $sudahDiperiksa = $daftarPasien->filter(fn($d) => $d->sudahDiperiksa())->count();
        $belumDiperiksa = $totalPasien - $sudahDiperiksa;

        return view('dokter.periksa-pasien.index', compact(
            'daftarPasien',
            'totalPasien',
            'sudahDiperiksa',
            'belumDiperiksa'
        ));
    }

    /**
     * bagian 2 tombol periksa
     */
    public function create($id)
    {
        $obats = Obat::orderBy('stok', 'asc')->get();
        $obatStokMenipis = $obats->filter(fn($o) => $o->isStokMenipis())->count();
        
        return view('dokter.periksa-pasien.create', compact('obats', 'id', 'obatStokMenipis'));
    }

    public function store(StorePeriksaPasienRequest $request)
    {
        try {
            $obatIds = json_decode($request->obat_json, true);

            // bagian 4 simpan data periksa
            DB::beginTransaction();

            // Validasi stok obat sekali lagi sebelum membuat periksa
            $obatHabis = [];
            foreach ($obatIds as $idObat) {
                $obat = Obat::find($idObat);
                if (!$obat || $obat->stok <= 0) {
                    $obatHabis[] = $obat ? $obat->nama_obat : 'Obat tidak ditemukan';
                }
            }

            if (!empty($obatHabis)) {
                DB::rollBack();
                return redirect()->back()
                    ->with('error', 'Stok obat berikut tidak tersedia: ' . implode(', ', $obatHabis))
                    ->withInput();
            }

            // Buat data periksa
            $periksa = Periksa::create([
                'id_daftar_poli' => $request->id_daftar_poli,
                'tgl_periksa' => now(),
                'catatan' => $request->catatan,
                'biaya_periksa' => $request->biaya_periksa + 150000,
            ]);

            // Simpan detail periksa dan kurangi stok obat
            $obatCount = 0;
            foreach ($obatIds as $idObat) {
                DetailPeriksa::create([
                    'id_periksa' => $periksa->id,
                    'id_obat' => $idObat,
                ]);

                // Kurangi stok obat
                $obat = Obat::find($idObat);
                if ($obat && $obat->stok > 0) {
                    $obat->decrement('stok');
                    $obatCount++;
                }
            }

            DB::commit();

            return redirect()->route('periksa-pasien.index')
                ->with('success', "Data periksa berhasil disimpan. ({$obatCount} obat diberikan)");

        } catch (\Exception $e) {
            DB::rollBack();
            
            \Log::error('Periksa Pasien Error: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'request' => $request->all(),
                'exception' => $e
            ]);

            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menyimpan data. Silakan coba lagi.')
                ->withInput();
        }
    }
}
