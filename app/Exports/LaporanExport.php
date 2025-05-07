<?php

namespace App\Exports;

use App\Models\StockMovement;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class LaporanExport implements FromView
{
    protected $startDate;
    protected $endDate;

    public function __construct($start, $end)
    {
        $this->startDate = $start;
        $this->endDate = $end;
    }

    public function view(): View
    {
        $stokIn = StockMovement::with('produk.barang')->where('type', 'in');
        $stokOut = StockMovement::with('produk.barang')->where('type', 'out');

        if ($this->startDate && $this->endDate) {
            $stokIn->whereBetween('created_at', [$this->startDate, $this->endDate]);
            $stokOut->whereBetween('created_at', [$this->startDate, $this->endDate]);
        }

        return view('export.laporan', [
            'stokIn' => $stokIn->get(),
            'stokOut' => $stokOut->get(),
        ]);
    }
}

