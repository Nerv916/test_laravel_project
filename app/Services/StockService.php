<?php
namespace App\Services;

use App\Models\Produk;
use App\Models\StockMovement;
use Exception;

class StockService
{
    public function adjustStock($produkId, $quantity, $type, $description = null)
    {
        $produk = Produk::findOrFail($produkId);

        if ($type == 'in') {
            $produk->stok += $quantity;
        } elseif ($type == 'out') {
            if ($produk->stok < $quantity) {
                throw new Exception('Stok tidak mencukupi');
            }
            $produk->stok -= $quantity;
        }

        $produk->save();

        StockMovement::create([
            'produk_id'  => $produkId,
            'type'       => $type,
            'quantity'   => $quantity,
            'description'=> $description
        ]);
    }
}
