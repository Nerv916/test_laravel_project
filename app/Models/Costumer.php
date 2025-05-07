<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Costumer extends Model
{
    protected $fillable = [
        'name',
        'alamat',
        'kontak',
        'pic',
        'npwp',
    ];
    public function Preorders(){
        return $this->hasMany(Preorder::class);
    }
}
