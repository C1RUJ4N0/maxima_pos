<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApartadoItem extends Model
{
    protected $fillable = ['apartado_id','product_id','quantity','price'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
