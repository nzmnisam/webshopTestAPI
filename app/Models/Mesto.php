<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mesto extends Model
{
    use HasFactory;

    protected $primaryKey = 'postanski_broj';
    public $incrementing = false;
    
    protected $fillable = [
        'postanski_boj',
        'naziv',
    ];
}
