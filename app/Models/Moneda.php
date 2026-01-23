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
        'region' => 'string',
        'direccion' => 'string',
        'simbolo_moneda' => 'string',
        'tasa_usd' => 'string',
    ];

    public function juegoMonedas()
    {
        return $this->hasMany(JuegoMoneda::class);
    }
}
