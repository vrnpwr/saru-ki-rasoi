<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemVariationOption extends Model
{
    use HasFactory;

    protected $fillable = ['item_variation_id', 'name', 'price'];

    public function variation()
    {
        return $this->belongsTo(ItemVariation::class);
    }
}
