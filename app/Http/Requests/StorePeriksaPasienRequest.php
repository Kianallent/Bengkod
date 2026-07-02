<?php

namespace App\Http\Requests;

use App\Models\Obat;
use Illuminate\Foundation\Http\FormRequest;

class StorePeriksaPasienRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'obat_json' => 'required|json',
            'catatan' => 'nullable|string|max:500',
            'biaya_periksa' => 'required|integer|min:0',
            'id_daftar_poli' => 'required|integer|exists:daftar_poli,id',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'obat_json.required' => 'Silakan pilih minimal satu obat.',
            'obat_json.json' => 'Format data obat tidak valid.',
            'catatan.max' => 'Catatan maksimal 500 karakter.',
            'biaya_periksa.required' => 'Biaya periksa harus diisi.',
            'biaya_periksa.integer' => 'Biaya periksa harus berupa angka.',
            'id_daftar_poli.required' => 'ID daftar poli tidak valid.',
            'id_daftar_poli.exists' => 'Pasien tidak ditemukan.',
        ];
    }

    /**
     * Validasi stok obat setelah validasi dasar.
     */
    public function withValidator($validator)
    {
        /**
     * bagian 3 validasi sebelum simpan
     */
        $validator->after(function ($validator) {
            $obatIds = json_decode($this->obat_json, true);
            
            if (!is_array($obatIds)) {
                $validator->errors()->add('obat_json', 'Data obat tidak valid.');
                return;
            }

            $obatHabis = [];
            $obatTidakDitemukan = [];

            foreach ($obatIds as $idObat) {
                $obat = Obat::find($idObat);
                
                if (!$obat) {
                    $obatTidakDitemukan[] = 'Obat ID #' . $idObat;
                } elseif ($obat->stok <= 0) {
                    $obatHabis[] = $obat->nama_obat . ' (Stok: ' . $obat->stok . ')';
                }
            }

            if (!empty($obatHabis)) {
                $validator->errors()->add('obat_json', 'Stok obat berikut tidak tersedia: ' . implode(', ', $obatHabis));
            }

            if (!empty($obatTidakDitemukan)) {
                $validator->errors()->add('obat_json', 'Obat berikut tidak ditemukan: ' . implode(', ', $obatTidakDitemukan));
            }
        });
    }
}
