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
        'juego_id' => 'string',
        'moneda_id' => 'string',
        'idioma' => 'string',
        'precio_oferta' => 'string',
        'precio_original' => 'string',
        'precio_oferta_anterior' => 'string',
        'precio_original_anterior' => 'string',
        'subtitulos' => 'string',
        'audio' => 'string',
        'link' => 'string',
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
