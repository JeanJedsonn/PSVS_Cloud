<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class JuegoMoneda extends Model
{
    /** @use HasFactory<\Database\Factories\JuegoMonedaFactory> */
    use HasFactory;

    protected $fillable = [
        'juego_id',
        'moneda_id',
        'idioma',
        'precio_oferta',
        'precio_original',
        'precio_oferta_anterior',
        'precio_original_anterior',
        'subtitulos',
        'audio',
        'link',
    ];

    public function juego()
    {
        return $this->belongsTo(Juego::class);
    }

    public function moneda(): BelongsTo
    {
        return $this->belongsTo(Moneda::class);
    }

    public function scopeOferta($query)
    {
        return $query->whereNotNull('precio_oferta');
    }
}
