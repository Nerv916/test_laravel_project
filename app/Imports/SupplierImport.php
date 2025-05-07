<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use App\Models\Supplier;
use Maatwebsite\Excel\Concerns\WithStartRow;

class SupplierImport implements ToCollection, WithStartRow
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
            $newSupplier = Supplier::where('name', $col[0])->first();
            if (!$newSupplier) {
                $save = new Supplier();
                $save->name = $col[0];
                $save->alamat = $col[1];
                $save->pic = $col[2];
                $save->kontak = $col[3];
                $save->npwp = $col[4];
                $save->pajak = $col[5];
                $save->save();
            }
        }
    }
}
