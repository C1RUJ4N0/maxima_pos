<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    use HasFactory;

    protected $fillable = [
        'total',
        'monto_recibido',
        'cambio',
        'user_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
