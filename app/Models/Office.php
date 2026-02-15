<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Office extends Model
{
    use HasFactory;
    protected $fillable =
    [
        'name',
        'descripcion'
    ];

    public function users()
    {
        return $this->hasMany(User::class, 'office_id');
    }
}
