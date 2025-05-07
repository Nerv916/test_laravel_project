<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use App\Models\Kategori;
use Maatwebsite\Excel\Concerns\WithStartRow;

class KategoriImport implements ToCollection, WithStartRow
{
    public function startRow(): int
    {
        return 2;
    }
    /**
     * @param Collection $collection
     */
    public function collection(Collection $collection)
    {
        foreach ($collection as $col) {
            $newKategori = Kategori::where('name', $col[0])->first();
            if (!$newKategori) {
                $save = new Kategori();
                $save->name = $col[0];
                $save->save();

            }
        }
    }
}
