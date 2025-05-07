<?php

namespace App\Imports;

use App\Models\Barang;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;

class BarangImport implements ToCollection, WithStartRow
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
       
            $newBarang = Barang::where('nama', $col[1])->first();
            if (!$newBarang) {
                $save = new Barang();
                $save->nama = $col[1];
                $save->save();
            }
        }
    }
}
