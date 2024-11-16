<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Zakat extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_name',
        'thumbnail', 
        'amount' => 0,
        'distribution' => 0,
    ];
}
