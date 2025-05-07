<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use App\Models\Costumer;
use Maatwebsite\Excel\Concerns\WithStartRow;

class CostumerImport implements ToCollection, WithStartRow
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
            // dd($col);
            $newCostumer = Costumer::where('name', $col[0])->first();
            if (!$newCostumer) {
                $save =  new Costumer();
                $save->name = $col[0];
                $save->alamat = $col[1];
                $save->pic = $col[2];
                $save->kontak = $col[3];
                $save->npwp = $col[4];
                $save->save();
            }
        }
    }
}
