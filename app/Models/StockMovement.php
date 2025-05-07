<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StockMovement extends Model
{
    protected $fillable = [
        'preorder_id',
        'approved_id',
        'produk_id',
        'parent_id',
        'approved_item_id',
        'no_batch',
        'exp_date',
        'harga_beli',
        'ppn',
        'total_harga_jual',
        'total_harga_beli',
        'type',
        'quantity',
        'description'
    ];

    public function produk()
    {
        return $this->belongsTo(Produk::class);
    }

    public function approvedItem()
    {
        return $this->belongsTo(ApprovedItems::class);
    }
    public function parent()
    {
        return $this->belongsTo(Preorder::class, 'parent_id');
    }
    public function approved()
    {
        return $this->belongsTo(Approved::class);
    }
    public function preorder()
    {
        return $this->belongsTo(Preorder::class);
    }
    
}
