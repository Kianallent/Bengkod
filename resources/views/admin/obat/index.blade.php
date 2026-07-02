<x-layouts.app title="Data Obat">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-slate-800">
            Data Obat
        </h2>

        <a href="{{ route('obat.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 
                  bg-primary hover:bg-primary/90 
                  text-white text-sm font-semibold 
                  rounded-xl transition">
            <i class="fas fa-plus text-xs"></i>
            Tambah Obat
        </a>
    </div>

    {{-- Alert Stok Menipis --}}
    @if($obatStokHabis > 0 || $obatStokMenipis > 0)
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
        @if($obatStokHabis > 0)
            <div class="alert alert-error shadow-md rounded-xl border-l-4 border-red-600">
                <div class="flex items-center gap-3">
                    <i class="fas fa-times-circle text-xl text-red-600"></i>
                    <div>
                        <h3 class="font-bold text-red-600">Stok Habis</h3>
                        <p class="text-sm text-red-600">{{ $obatStokHabis }} obat tidak tersedia</p>
                    </div>
                </div>
            </div>
        @endif

        @if($obatStokMenipis > 0)
            <div class="alert alert-warning shadow-md rounded-xl border-l-4 border-orange-500">
                <div class="flex items-center gap-3">
                    <i class="fas fa-exclamation-triangle text-xl text-orange-600"></i>
                    <div>
                        <h3 class="font-bold text-orange-600">Stok Menipis</h3>
                        <p class="text-sm text-orange-600">{{ $obatStokMenipis }} obat dengan stok ≤ 5</p>
                    </div>
                </div>
            </div>
        @endif
    </div>
    @endif

    {{-- Card --}}
    <div class="card bg-base-100 shadow-md rounded-2 border">
        <div class="card-body p-0">

            <div class="overflow-x-auto">
                <table class="table w-full">

                    {{-- Table Head --}}
                    <thead class="bg-slate-50 text-slate-500 uppercase text-xs tracking-wider">
                        <tr>
                            <th class="px-6 py-4">Nama Obat</th>
                            <th class="px-6 py-4">Stok</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4">Kemasan</th>
                            <th class="px-6 py-4">Harga</th>
                            <th class="px-6 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>

                    {{-- Table Body --}}
                    <tbody class="text-sm text-slate-700">
                        @forelse($obats as $obat)
                        <tr class="border-t border-slate-100 hover:bg-slate-50 transition @if($obat->isStokHabis()) bg-red-50 @elseif($obat->isStokMenipis()) bg-orange-50 @endif">

                            <td class="px-6 py-4 font-semibold text-slate-800">
                                {{ $obat->nama_obat }}
                            </td>

                            <td class="px-6 py-4 font-bold text-slate-800">
                                <span class="@if($obat->isStokHabis()) text-red-600 @elseif($obat->isStokMenipis()) text-orange-600 @else text-green-600 @endif">
                                    {{ $obat->stok ?? 0 }}
                                </span>
                            </td>

                            <td class="px-6 py-4">
                                @if($obat->isStokHabis())
                                    <span class="inline-flex items-center gap-1 px-3 py-1 text-xs font-bold rounded-full bg-red-100 text-red-600">
                                        <i class="fas fa-circle text-xs"></i>
                                        Habis
                                    </span>
                                @elseif($obat->isStokMenipis())
                                    <span class="inline-flex items-center gap-1 px-3 py-1 text-xs font-bold rounded-full bg-orange-100 text-orange-600">
                                        <i class="fas fa-triangle-exclamation text-xs"></i>
                                        Menipis
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-3 py-1 text-xs font-bold rounded-full bg-green-100 text-green-600">
                                        <i class="fas fa-check-circle text-xs"></i>
                                        Normal
                                    </span>
                                @endif
                            </td>

                            <td class="px-6 py-4">
                                <span class="inline-block px-3 py-1 text-xs font-semibold 
                                             rounded-full bg-blue-100 text-blue-600">
                                    {{ $obat->kemasan ?? '-' }}
                                </span>
                            </td>

                            <td class="px-6 py-4 font-semibold text-slate-800">
                                Rp {{ number_format($obat->harga, 0, ',', '.') }}
                            </td>

                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end gap-2">

                                    {{-- Edit --}}
                                    <a href="{{ route('obat.edit', $obat->id) }}" class="inline-flex items-center gap-1 px-4 py-2 
                                              bg-amber-500 hover:bg-amber-600 
                                              text-white text-xs font-semibold 
                                              rounded-lg transition">
                                        <i class="fas fa-pen text-xs"></i>
                                        Edit
                                    </a>

                                    {{-- Delete --}}
                                    <form action="{{ route('obat.destroy', $obat->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')

                                        <button type="submit"
                                            onclick="return confirm('Yakin ingin menghapus obat ini?')" class="inline-flex items-center gap-1 px-4 py-2 
                                                   bg-red-500 hover:bg-red-600 
                                                   text-white text-xs font-semibold 
                                                   rounded-lg transition">
                                            <i class="fas fa-trash text-xs"></i>
                                            Hapus
                                        </button>
                                    </form>

                                </div>
                            </td>

                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-12 text-slate-400">
                                <i class="fas fa-inbox text-3xl mb-3 block"></i>
                                Belum ada data obat
                            </td>
                        </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>

        </div>
    </div>

</x-layouts.app>