<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Moneda extends Model
{
    /** @use HasFactory<\Database\Factories\RegionMonedaFactory> */
    use HasFactory;

    protected $fillable = [
        'region',
        'direccion',
        'simbolo_moneda',
        'tasa_usd',
    ];

    public function juegoMonedas()
    {
        return $this->hasMany(JuegoMoneda::class);
    }
}
