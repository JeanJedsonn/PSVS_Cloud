<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Juego extends Model
{
    /** @use HasFactory<\Database\Factories\JuegoFactory> */
    use HasFactory;
    //public $incrementing = false;
    //protected $keyType = 'string';
    protected $fillable = [
        'id' => 'string',
        'titulo' => 'string',
        'descripcion' => 'string',
        'plataforma' => 'string',
        'imgURL' => 'string',
        'imgLowURL' => 'string',
        'id_sony' => 'string',
        'oferta' => 'integer',
        'importancia' => 'integer'
    ];

    public function monedas():HasManyThrough
    {
        return $this->hasManyThrough(Moneda::class, JuegoMoneda::class)->withPivot('precio','idioma');
    }

    public function juegoMonedas()
    {
        return $this->hasMany(JuegoMoneda::class, 'juego_id', 'id');
    }
}
