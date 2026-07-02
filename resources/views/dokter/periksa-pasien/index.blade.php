<x-layouts.app title="Periksa Pasien">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-slate-800">
            Periksa Pasien
        </h2>
    </div>

    {{-- Statistik --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="card bg-blue-50 rounded-xl border border-blue-200">
            <div class="card-body p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-semibold text-blue-600">Total Pasien</h3>
                        <p class="text-2xl font-bold text-blue-700 mt-2">{{ $totalPasien }}</p>
                    </div>
                    <i class="fas fa-users text-3xl text-blue-300"></i>
                </div>
            </div>
        </div>

        <div class="card bg-green-50 rounded-xl border border-green-200">
            <div class="card-body p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-semibold text-green-600">Sudah Diperiksa</h3>
                        <p class="text-2xl font-bold text-green-700 mt-2">{{ $sudahDiperiksa }}</p>
                    </div>
                    <i class="fas fa-circle-check text-3xl text-green-300"></i>
                </div>
            </div>
        </div>

        <div class="card bg-orange-50 rounded-xl border border-orange-200">
            <div class="card-body p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-semibold text-orange-600">Belum Diperiksa</h3>
                        <p class="text-2xl font-bold text-orange-700 mt-2">{{ $belumDiperiksa }}</p>
                    </div>
                    <i class="fas fa-hourglass-half text-3xl text-orange-300"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Alert Flash Message --}}
    @if (session('message'))
    <div class="alert alert-{{ session('type') ?? 'success' }} mb-4 rounded-xl shadow-sm" role="alert">
        <i class="fas fa-{{ session('type') == 'danger' ? 'circle-xmark' : 'circle-check' }}"></i>
        <span>{{ session('message') }}</span>
    </div>
    @endif

    {{-- Card --}}
    <div class="card bg-base-100 shadow-md rounded-2xl border">
        <div class="card-body p-6">
            <h3 class="text-lg font-bold text-slate-800 mb-6">Daftar Pasien</h3>

            <div class="overflow-x-auto">
                <table class="table w-full">

                    {{-- Head --}}
                    <thead class="bg-slate-50 text-slate-600 text-xs uppercase tracking-wider font-semibold">
                        <tr>
                            <th class="px-6 py-4">No</th>
                            <th class="px-6 py-4">Pasien</th>
                            <th class="px-6 py-4">No. RM</th>
                            <th class="px-6 py-4">Keluhan</th>
                            <th class="px-6 py-4">Antrian</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>

                    {{-- Body --}}
                    <tbody class="text-sm text-slate-700">
                        @forelse ($daftarPasien as $dp)
                        <tr class="border-t border-slate-100 hover:bg-slate-50 transition @if($dp->sudahDiperiksa()) bg-green-50 @endif">

                            <td class="px-6 py-4 font-semibold text-slate-600">
                                {{ $loop->iteration }}
                            </td>

                            <td class="px-6 py-4">
                                <div>
                                    <p class="font-semibold text-slate-800">{{ $dp->pasien->nama }}</p>
                                    <p class="text-xs text-slate-500">{{ $dp->pasien->no_hp }}</p>
                                </div>
                            </td>

                            <td class="px-6 py-4 font-mono text-slate-600">
                                {{ $dp->pasien->no_rm ?? '-' }}
                            </td>

                            <td class="px-6 py-4 max-w-sm">
                                <span class="text-slate-700">{{ Str::limit($dp->keluhan, 40) }}</span>
                            </td>

                            <td class="px-6 py-4 text-center">
                                <span class="inline-block px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-bold">
                                    #{{ $dp->no_antrian }}
                                </span>
                            </td>

                            <td class="px-6 py-4">
                                @if ($dp->sudahDiperiksa())
                                    <span class="inline-flex items-center gap-1 px-3 py-1 text-xs font-bold rounded-full bg-green-100 text-green-700">
                                        <i class="fas fa-circle-check"></i>
                                        Diperiksa
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-3 py-1 text-xs font-bold rounded-full bg-orange-100 text-orange-700">
                                        <i class="fas fa-hourglass-half"></i>
                                        Menunggu
                                    </span>
                                @endif
                            </td>

                            <td class="px-6 py-4 text-right">
                                @if ($dp->sudahDiperiksa())
                                    <button onclick="openDetailPeriksa({{ $dp->id }})"
                                        class="btn btn-sm bg-sky-500 hover:bg-sky-600 text-white border-none rounded-lg px-4">
                                        <i class="fas fa-eye"></i>
                                        Lihat
                                    </button>
                                @else
                                    <a href="{{ route('periksa-pasien.create', $dp->id) }}"
                                        class="btn btn-sm bg-amber-500 hover:bg-amber-600 text-white border-none rounded-lg px-4">
                                        <i class="fas fa-stethoscope"></i>
                                        Periksa
                                    </a>
                                @endif
                            </td>

                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-16 text-slate-400">
                                <div class="flex flex-col items-center justify-center gap-3">
                                    <i class="fas fa-inbox text-4xl"></i>
                                    <span class="font-semibold">Tidak ada data pasien</span>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>

        </div>
    </div>

    <script>
        // Auto-hide success message
        setTimeout(() => {
            const alert = document.querySelector('.alert');
            if (alert) {
                alert.classList.remove('show');
                alert.classList.add('fade');
                setTimeout(() => alert.remove(), 200);
            }
        }, 3000);

        function openDetailPeriksa(daftarPoliId) {
            // Implementasi untuk menampilkan detail periksa (opsional)
            console.log('Detail periksa for:', daftarPoliId);
        }
    </script>

</x-layouts.app>