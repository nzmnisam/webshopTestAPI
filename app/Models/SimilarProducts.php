<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SimilarProducts extends Model
{
    use HasFactory;

    protected $fillable = [
        'product1_id',
        'product2_id',
    ];
}
