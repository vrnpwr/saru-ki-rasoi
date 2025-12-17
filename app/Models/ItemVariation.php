<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemVariation extends Model
{
    use HasFactory;

    protected $fillable = ['item_id', 'name', 'type', 'required'];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function options()
    {
        return $this->hasMany(ItemVariationOption::class);
    }
}
