<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bundling extends Model
{
    use HasFactory;

    protected $table = 'bundlings';
    protected $guarded = [];

    public function bundles()
    {
        return $this->belongsToMany(Bundling::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class);
    }

}
