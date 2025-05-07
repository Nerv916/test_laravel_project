<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Approved extends Model
{
    protected $fillable = [
        'preorder_id',
        'parent_id',
        'no_spo',
        'name_cust',
        'no_surat',
        'order_date'
    ];
    public function items()
    {
        return $this->hasMany(ApprovedItems::class, 'approved_id');
    }
    public function getTotalBelanjaAttribute()
    {
        return $this->items()->sum('total_harga');
    }
    public function stockMovements()
    {
        return $this->hasManyThrough(
            StockMovement::class,
            ApprovedItems::class,
            'approved_id',        // Foreign key di ApprovedItems
            'approved_item_id',   // Foreign key di StockMovement
            'id',                 // Local key di Approved
            'id'                  // Local key di ApprovedItems
        )->where('type', 'in');
    }
    public function preorder()
    {
        return $this->belongsTo(Preorder::class);
    }
    public function customer()
    {
        return $this->belongsTo(Costumer::class, 'customer_id', 'id');
    }
}
