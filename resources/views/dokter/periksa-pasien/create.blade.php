<x-layouts.app title="Periksa Pasien">

    {{-- Header --}}
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('periksa-pasien.index') }}" class="inline-flex items-center justify-center w-9 h-9 
                  rounded-lg bg-slate-100 text-slate-500 
                  hover:bg-slate-200 transition">
            <i class="fas fa-arrow-left text-sm"></i>
        </a>
        <h2 class="text-2xl font-bold text-slate-800">
            Periksa Pasien
        </h2>
    </div>

    {{-- Alert Stok Menipis --}}
    @if($obatStokMenipis > 0)
    <div class="alert alert-warning shadow-md mb-6 rounded-lg border-l-4 border-orange-500">
        <div class="flex items-center gap-3">
            <i class="fas fa-exclamation-triangle text-lg text-orange-600"></i>
            <div>
                <h3 class="font-bold text-orange-600">Perhatian: Stok Obat Menipis</h3>
                <p class="text-sm text-orange-600">{{ $obatStokMenipis }} obat dengan stok ≤ 5. Mohon perhatikan saat memberikan obat.</p>
            </div>
        </div>
    </div>
    @endif

    {{-- Alert Error --}}
    <x-alert-errors :title="'Validasi Gagal'" />

    {{-- Card --}}
    <div class="card bg-base-100 shadow-sm rounded-2xl border border-slate-200">
        <div class="card-body p-8">

            <form action="{{ route('periksa-pasien.store') }}" method="POST">
                @csrf
                <input type="hidden" name="id_daftar_poli" value="{{ $id }}">

                {{-- Pilih Obat --}}
                <div class="form-control mb-5">
                    <label class="label pb-1">
                        <span class="text-sm font-semibold text-gray-700">Pilih Obat <span class="text-red-500">*</span></span>
                    </label>
                    <select id="select-obat" class="select select-bordered w-full rounded-lg border-2 px-4">
                        <option value="">-- Pilih Obat --</option>
                        @foreach ($obats as $obat)
                            <option value="{{ $obat->id }}"
                                data-nama="{{ $obat->nama_obat }}"
                                data-harga="{{ $obat->harga }}"
                                data-stok="{{ $obat->stok }}"
                                data-status="{{ $obat->getStatusStok() }}">
                                {{ $obat->nama_obat }} - Rp{{ number_format($obat->harga) }} 
                                @if($obat->isStokHabis())
                                    <span class="text-red-600 font-bold">(Stok: HABIS)</span>
                                @elseif($obat->isStokMenipis())
                                    <span class="text-orange-600 font-bold">(Stok: {{ $obat->stok }} - MENIPIS)</span>
                                @else
                                    <span class="text-green-600">(Stok: {{ $obat->stok }})</span>
                                @endif
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Alert Stok Terbatas --}}
                <div id="alert-stok" class="alert alert-warning shadow-md mb-5 rounded-lg border-l-4 border-orange-500 hidden">
                    <div>
                        <i class="fas fa-exclamation-triangle text-lg text-orange-600"></i>
                        <div class="ml-3">
                            <h3 class="font-bold text-orange-600">Perhatian: Stok Obat Terbatas</h3>
                            <p class="text-sm text-orange-600" id="alert-stok-message"></p>
                        </div>
                    </div>
                </div>

                {{-- Obat Terpilih --}}
                <div class="form-control mb-5">
                    <label class="label pb-1 ">
                        <span class="text-sm font-semibold text-gray-700">Obat Terpilih</span>
                    </label>

                    <ul id="obat-terpilih" class="flex flex-col gap-2 mb-2 min-h-[48px]"></ul>

                    <input type="hidden" name="biaya_periksa" id="biaya_periksa" value="0">
                    <input type="hidden" name="obat_json" id="obat_json">
                </div>

                {{-- Total Harga --}}
                <div class="form-control mb-5">
                    <label class="label pb-1">
                        <span class="text-sm font-semibold text-gray-700">Total Harga</span>
                    </label>
                    <div class="input input-bordered w-full rounded-lg flex items-center bg-slate-50 text-slate-700 font-bold" id="total-harga">
                        Rp 0
                    </div>
                </div>

                {{-- Catatan --}}
                <div class="form-control mb-8">
                    <label class="label pb-1">
                        <span class="text-sm font-semibold text-gray-700">Catatan <span class="text-slate-400 font-normal">(Opsional)</span></span>
                    </label>
                    <textarea name="catatan" id="catatan" rows="4"
                        placeholder="Masukkan catatan..."
                        class="textarea textarea-bordered w-full border-2 px-4 py-2 rounded-lg resize-none">{{ old('catatan') }}</textarea>
                </div>

                {{-- Buttons --}}
                <div class="flex gap-3">
                    <button type="submit"
                        class="btn bg-[#2d4499] hover:bg-[#1e2d6b] text-white border-none rounded-lg px-6">
                        <i class="fas fa-save"></i>
                        Simpan
                    </button>
                    <a href="{{ route('periksa-pasien.index') }}"
                        class="btn btn-ghost bg-slate-100 hover:bg-slate-200 text-slate-500 rounded-lg px-6">
                        Batal
                    </a>
                </div>

            </form>
        </div>
    </div>

    <script>
        const selectObat = document.getElementById('select-obat');
        const listObat = document.getElementById('obat-terpilih');
        const inputBiaya = document.getElementById('biaya_periksa');
        const inputObatJson = document.getElementById('obat_json');
        const totalHargaEl = document.getElementById('total-harga');
        const alertStok = document.getElementById('alert-stok');
        const alertStokMessage = document.getElementById('alert-stok-message');

        let daftarObat = [];

        selectObat.addEventListener('change', () => {
            const selectedOption = selectObat.options[selectObat.selectedIndex];
            const id = selectedOption.value;
            const nama = selectedOption.dataset.nama;
            const harga = parseInt(selectedOption.dataset.harga || 0);
            const stok = parseInt(selectedOption.dataset.stok || 0);
            const status = selectedOption.dataset.status || 'Normal';

            if (!id) {
                alertStok.classList.add('hidden');
                return;
            }

            // Validasi stok obat
            if (stok <= 0) {
                alertStok.classList.remove('hidden');
                alertStokMessage.innerHTML = `<strong>❌ Obat "${nama}" sudah habis (Stok: ${stok}).</strong> Tidak dapat dipilih.`;
                selectObat.selectedIndex = 0;
                return;
            }

            // Tampilkan warning jika stok terbatas
            if (stok <= 5) {
                alertStok.classList.remove('hidden');
                alertStokMessage.innerHTML = `<strong>⚠️ Obat "${nama}" memiliki stok terbatas (Stok: ${stok}).</strong> Mohon perhatikan saat memberi obat.`;
            } else {
                alertStok.classList.add('hidden');
            }

            // Cek duplikat obat
            if (daftarObat.some(o => o.id == id)) {
                alert('Obat ini sudah dipilih!');
                selectObat.selectedIndex = 0;
                return;
            }

            daftarObat.push({ id, nama, harga, stok, status });
            renderObat();
            selectObat.selectedIndex = 0;
        });

        function renderObat() {
            listObat.innerHTML = '';
            let total = 0;

            daftarObat.forEach((obat, index) => {
                total += obat.harga;

                const item = document.createElement('li');
                item.className = 'flex items-center justify-between px-4 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm text-slate-700';
                
                let stokBadgeClass = 'bg-green-100 text-green-600';
                let stokBadgeText = '✓ Normal';
                
                if (obat.stok <= 0) {
                    stokBadgeClass = 'bg-red-100 text-red-600 font-bold';
                    stokBadgeText = '❌ Habis';
                } else if (obat.stok <= 5) {
                    stokBadgeClass = 'bg-orange-100 text-orange-600 font-bold';
                    stokBadgeText = '⚠️ Menipis';
                }
                
                item.innerHTML = `
                    <span>
                        ${obat.nama} — <span class="font-semibold">Rp ${obat.harga.toLocaleString()}</span>
                        <span class="inline-block ml-2 px-2 py-1 text-xs font-semibold rounded-full ${stokBadgeClass}">
                            ${stokBadgeText} (S: ${obat.stok})
                        </span>
                    </span>
                    <button type="button"
                        onclick="hapusObat(${index})"
                        class="btn btn-sm bg-red-500 hover:bg-red-600 text-white border-none rounded-lg px-3">
                        <i class="fas fa-trash"></i>
                    </button>
                `;
                listObat.appendChild(item);
            });

            inputBiaya.value = total;
            totalHargaEl.textContent = `Rp ${total.toLocaleString()}`;
            inputObatJson.value = JSON.stringify(daftarObat.map(o => o.id));
        }

        function hapusObat(index) {
            daftarObat.splice(index, 1);
            renderObat();
            alertStok.classList.add('hidden');
        }
    </script>

</x-layouts.app>