<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kupuje extends Model
{
    use HasFactory;
    
    protected $primaryKey = ['UserID', 'ProductID'];//nije radilo, morao sam u sqlyog da namestim da je primary key
    public $incrementing = false;
    
    protected $fillable = [
        'UserID',
        'ProductID',
        'date',
    ];
}
