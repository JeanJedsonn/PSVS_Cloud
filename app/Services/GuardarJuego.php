<?php
namespace App\Services;
use App\Models\Juego;
use App\Models\JuegoMoneda;
use App\Models\Moneda;
use Illuminate\Support\Facades\Log;

use function PHPSTORM_META\type;

class GuardarJuego
{
    //protected $buscador;

    public function guardar($consulta): bool
    {
        $juego = new Juego();
        $titulo = Juego::where('titulo', $consulta['titulo'])->first();     //busqueda por titulo
        $idSony = Juego::where('id_sony', $consulta['codigo'])->first();    //busqueda por codigo
        $responseJuego = true;
        $responseMoneda = true;

        // Verificar si el juego ya existe (ambos deben dar falso)
        if (!$idSony && !$titulo) {
            // creacion del modelo y asignacion de relaciones
            Log::info("route:Services/GuardarJuego.php> Guardando juego: {$consulta['titulo']}");
            $juego->titulo = $consulta['titulo'];
            $juego->plataforma = $consulta['consola'];
            $juego->imgLowURL = str_replace("w=1024", 'w=256', $consulta['imagenURL']);
            $juego->imgURL = $consulta['imagenURL'];
            $juego->id_sony = $consulta['codigo'];
            $juego->oferta = $consulta['oferta'] ? 1 : 0;

            $responseJuego = $juego->save();

        } else {
            // ya sea que el codigo o el titulo dierean verdad, hay que cambiar todo
            Log::info("route:Services/GuardarJuego.php> Actualizando juego: {$consulta['titulo']} e ID {$consulta['codigo']}");
            $responseJuego = $idSony->update([
                'titulo' => $consulta['titulo'],
                'id_sony' => $consulta['codigo'],
                'plataforma' => $consulta['consola'],
                'imgLowURL' => str_replace("w=1024", 'w=256', $consulta['imagenURL']),
                'imgURL' => $consulta['imagenURL'],
                'oferta' => $consulta['oferta'] ? 1 : 0,
            ]);
            $juego = Juego::find($idSony->id);   //para usar el mismo objeto en la siguiente parte
        }

        $juegoMonedas = $juego->juegoMonedas;
        // entradas limpiadas, proceder a actualizar o crear nuevas
        foreach ($consulta['precioOriginal'] as $region => $precio) {
            // Actualizar o crear el precio por region
            $juegoMoneda = $juegoMonedas->where('moneda.direccion', $region)->first();

            if ($juegoMoneda) {
                // Actualizar los precios existentes
                $responseMoneda = $this->actualizarJuegoMonedas($juegoMoneda, $region, $precio, $consulta);
            } else {
                // Crear un nuevo registro si no existe
                $responseMoneda = $this->guardarJuegoMonedas($region, $precio, $juego->id, $consulta);
            }
        }

        return $responseMoneda && $responseJuego;
    }

    private function guardarJuegoMonedas($region, $precio, $juegoID, $consulta)
    {
        $response = false;
        // creacion del modelo
        $juegoMonedaNuevo = new JuegoMoneda();
        $juegoMonedaNuevo->juego_id = $juegoID;
        $juegoMonedaNuevo->moneda_id = Moneda::where('direccion', $region)->first()->id;

        // Asignacion de valores
        $juegoMonedaNuevo->precio_original = $precio;
        $juegoMonedaNuevo->precio_oferta = $consulta['precioActual'][$region];
        $juegoMonedaNuevo->precio_original_anterior = "";
        $juegoMonedaNuevo->precio_oferta_anterior = "";
        $juegoMonedaNuevo->subtitulos = "Pendiente"; //TODO: Buscar idioma en la pagina de sony o agregar un boton para ingresar el idioma
        $juegoMonedaNuevo->audio = "Pendiente";
        $juegoMonedaNuevo->link = $consulta['link'][$region];
        $response = $juegoMonedaNuevo->save();
        Log::info("route:Services/GuardarJuego.php> Guardando juegoMoneda: {$juegoMonedaNuevo->id}");
        return $response;
    }

    private function actualizarJuegoMonedas($juegoMoneda, $region, $precio, $consulta)
    {
        $response = $juegoMoneda->update([
            'precio_original' => $precio,
            'precio_oferta' => $consulta['precioActual'][$region],
            'subtitulos' => "Pendiente", //TODO: Buscar idioma en la pagina de sony o agregar un boton para ingresar el idioma
            'audio' => "Pendiente",
            'precio_original_anterior' => "",   //TODO: Buscar precio original en la pagina de sony o agregar un boton para ingresar el precio original
            'precio_oferta_anterior' => "",   //TODO: Buscar precio oferta en la pagina de sony o agregar un boton para ingresar el precio oferta
            "link" => $consulta['link'][$region],
        ]);
        Log::info("route:Services/GuardarJuego.php> Actualizando juegoMoneda: {$juegoMoneda->id}");
        return $response;
    }
}
