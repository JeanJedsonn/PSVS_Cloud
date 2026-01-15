<?php
namespace App\Services;

use App\Models\Juego;
use Illuminate\Database\Eloquent\Builder;

class ConstructorConsultasJuego
{
    public function construirQuery(
        string $ordenarPor,
        string $ordenDireccion,
        ?string $busqueda = null
    ): Builder {
        if ($ordenarPor === 'tiene_oferta') {
            return $this->queryConOrdenamientoPorOferta($ordenDireccion, $busqueda);
        }

        return $this->queryConOrdenamientoSimple($ordenarPor, $ordenDireccion, $busqueda);
    }

    protected function queryConOrdenamientoPorOferta(
        string $ordenDireccion,
        ?string $busqueda
    ): Builder {
        $query = Juego::with('juegoMonedas.moneda')
            ->orderBy('oferta', $ordenDireccion);

        if ($busqueda) {
            $query->where('titulo', 'ILIKE', '%' . $busqueda . '%');
        }

        return $query;
    }

/* Metodo anterior
    protected function queryConOrdenamientoPorOferta(
        string $ordenDireccion,
        ?string $busqueda
    ): Builder {
        $query = Juego::select('juegos.*')
            ->leftJoin('juego_monedas', 'juegos.id', '=', 'juego_monedas.juego_id')
            ->with('juegoMonedas.moneda')
            ->groupBy('juegos.id')
            ->orderByRaw(
                'MAX(CASE WHEN juego_monedas.precio_oferta IS NOT NULL AND juego_monedas.precio_oferta != \'-\' THEN 1 ELSE 0 END) '
                . $ordenDireccion
            );

        if ($busqueda) {
            $query->where('juegos.titulo', 'ILIKE', '%' . $busqueda . '%');
        }

        return $query;
    }
*/

    protected function queryConOrdenamientoSimple(
        string $ordenarPor,
        string $ordenDireccion,
        ?string $busqueda
    ): Builder {
        $query = Juego::with('juegoMonedas.moneda')
            ->orderBy($ordenarPor, $ordenDireccion);

        if ($busqueda) {
            $query->where('titulo', 'ILIKE', '%' . $busqueda . '%');
        }

        return $query;
    }
}
