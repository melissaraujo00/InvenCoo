<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class office extends Model
{
    use HasFactory;
    protected $fillable =
    [
        'name',
        'descripcion'
    ];
}
