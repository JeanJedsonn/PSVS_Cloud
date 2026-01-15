<?php

namespace App\Services;

//use App\Models\Juego;
use Illuminate\Support\Facades\Log;
use App\Services\BuscadorJuego;
use App\Services\GuardarJuego;
use App\Models\Juego;

class ActualizadorJuegos
{
    protected BuscadorJuego $buscador;
    protected GuardarJuego $guardador;

    public function __construct(BuscadorJuego $buscador, GuardarJuego $guardador)
    {
        $this->buscador = $buscador;
        $this->guardador = $guardador;
    }

    /**
     * Actualizar información de un juego específico
     */
    public function actualizarJuego(int $id): bool
    {
        try {
            $juego = Juego::find($id);
            $juegoActualizar = '';

            $respuesta = $this->buscador->buscar($juego->titulo);

            if (isset($respuesta[$juego->id_sony])) {
                $juegoActualizar = $respuesta[$juego->id_sony];
                Log::info("route:Services/ActualizadorJuegos.php> Juego {$juego->titulo} encontrado");
            } else {
                Log::info("route:Services/ActualizadorJuegos.php> Juego {$juego->titulo} no encontrado por codigo");
                foreach ($respuesta as $key => $evaluar) {
                    Log::info("Evaluando {$evaluar['titulo']} contra {$juego->titulo}");
                    if ($evaluar['titulo'] == $juego->titulo) {
                        $juegoActualizar = $evaluar;
                        Log::info("route:Services/ActualizadorJuegos.php> Juego {$juego->titulo} encontrado por titulo");
                        break;
                    }
                }
            }



            if (empty($juegoActualizar)) {
                Log::alert("route:Services/ActualizadorJuegos.php> Juego {$juego->titulo} no encontrado");
                return false;
            }
            return $this->guardador->guardar($juegoActualizar);


        } catch (\Exception $e) {
            Log::error("route:Services/ActualizadorJuegos.php> Error actualizando juego {$juego}: " . $e->getMessage());
            return false;   // Devuelve false si la actualización falló
        }
    }

}
