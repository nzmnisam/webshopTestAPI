<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kupuje extends Model
{
    use HasFactory;
    
    protected $primaryKey = ['user_id', 'product_id'];//nije radilo, morao sam u sqlyog da namestim da je primary key
    public $incrementing = false;
    
    protected $fillable = [
        'user_id',
        'product_id',
    ];
}
