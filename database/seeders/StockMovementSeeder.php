<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\Produk;
use App\Models\Approved;
use App\Models\Preorder;
use Illuminate\Support\Str;
use App\Models\ApprovedItems;
use App\Models\StockMovement;
use Illuminate\Database\Seeder;

class StockMovementSeeder extends Seeder
{
    public function run(): void
    {
        $preorders = Preorder::inRandomOrder()->take(5)->get();
        $produks = Produk::inRandomOrder()->take(5)->get();
        $approveds = Approved::inRandomOrder()->take(5)->get();
        $approvedItems = ApprovedItems::inRandomOrder()->take(5)->get();

        foreach (['in', 'out'] as $type) {
            for ($i = 0; $i < 10; $i++) {
                $preorder = $preorders->random();
                $produk = $produks->random();
                $approved = $approveds->random();
                $approvedItem = $approvedItems->random();

                $qty = rand(1, 20);
                $hargaBeli = rand(10000, 100000);
                $totalBeli = $qty * $hargaBeli;
                $totalJual = $totalBeli + rand(10000, 50000); // markup sedikit

                StockMovement::create([
                    'preorder_id' => $preorder->id,
                    'produk_id' => $produk->id,
                    'approved_id' => $approved->id,
                    'approved_item_id' => $approvedItem->id,
                    'parent_id' => rand(0, 1) ? $preorders->random()->id : null,
                    'harga_beli' => $hargaBeli,
                    'ppn' => 0.00,
                    'no_batch' => Str::upper(Str::random(8)),
                    'exp_date' => now()->addMonths(rand(6, 24)),
                    'total_harga_beli' => $totalBeli,
                    'total_harga_jual' => $totalJual,
                    'type' => $type,
                    'quantity' => $qty,
                    'description' => $type === 'in' ? 'Barang masuk dari supplier' : 'Barang keluar untuk penjualan',
                ]);
            }
        }
    }

}
