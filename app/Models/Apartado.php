<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Apartado extends Model
{
    use HasFactory;

    protected $fillable = [
        'cliente',
        'telefono',
        'monto',
        'fecha_vencimiento'
    ];
}
