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

    /* Esta version no contempla que sony modificase informacion del juego
    public function guardar($consulta): bool
    {
        $juego = new Juego();
        $condiciones = [
            ['titulo', '=', $consulta['titulo']],
            ['plataforma', '=', $consulta['consola']],
            ['id_sony', '=', $consulta['codigo']]
        ];

        $juego = Juego::where($condiciones)->first();
        $responseJuego = true;
        $responseMoneda = true;
        $actualizarJuego = false;

        #No existe el Titulo ni el ID: El juego es nuevo y simplemente se agrega
        if ($juego->isEmpty()) {
           // creacion del modelo y asignacion de relaciones
            Log::info("route:Services/GuardarJuego.php> Guardando juego: {$consulta['titulo']}");
            $juego->titulo = $consulta['titulo'];
            $juego->plataforma = $consulta['consola'];
            $juego->imgLowURL = str_replace("w=1024", 'w=256', $consulta['imagenURL']);
            $juego->imgURL = $consulta['imagenURL'];
            $juego->id_sony = $consulta['codigo'];
            $juego->oferta = $consulta['oferta'] ? 1 : 0;

            $responseJuego = $juego->save();
        }
        


        if($actualizarJuego) {
            // ya sea que el codigo o el titulo dierean verdad, hay que cambiar todo
            Log::info("route:Services/GuardarJuego.php> Actualizando juego: {$consulta['titulo']} e ID {$consulta['codigo']}");
            $responseJuego = $juego->update([
                'titulo' => $consulta['titulo'],
                'id_sony' => $consulta['codigo'],
                'plataforma' => $consulta['consola'],
                'imgLowURL' => str_replace("w=1024", 'w=256', $consulta['imagenURL']),
                'imgURL' => $consulta['imagenURL'],
                'oferta' => $consulta['oferta'] ? 1 : 0,
            ]);
            $juego = Juego::find($juego->id);   //para usar el mismo objeto en la siguiente parte
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
    */

    
    public function guardar($consulta): bool
    {
        $juego = new Juego();
        $titulo = Juego::where('titulo', $consulta['titulo'])->get();              //busqueda por titulo
        $idSony = Juego::where('id_sony', $consulta['codigo'])->get();             //busqueda por codigo
        $responseJuego = true;
        $responseMoneda = true;
        $actualizarJuego = false;

        #Existe el Titulo y el ID: Hay que comprobar si existe mas de un titulo
        if($titulo->isNotEmpty() && $idSony->isNotEmpty()) {
            #existe un titulo y un codigo:
            if($titulo->count() == 1 && $idSony->count() == 1) {
                #confirmar que es el mismo juego
                if ($titulo->first()->titulo == $idSony->first()->titulo && $titulo->first()->plataforma == $idSony->first()->plataforma) {
                    $actualizarJuego = true;
                    $juegoExistente = $idSony->first();
                }
            }

            #existe un titulo y mas de un codigo:
            if ($titulo->count() == 1 && $idSony->count() > 1) {
                #comprobar que juegos tienen el mismo titulo y consola
                $temp_juegos_titulo = $idSony->where('titulo', $consulta['titulo'])->where('plataforma', $consulta['consola'])->get();
                if ($temp_juegos_titulo->count() != 1) {
                    #Error de integridad
                    Log::error("route:Services/GuardarJuego.php> Error de integridad: {$consulta['titulo']} y {$consulta['consola']}");
                    dd("Error de integridad, existe el titulo y mas de un codigo");
                }
                else{
                    #se encontro la asociacion, se requiere actualizar el juego
                    $juegoExistente = $temp_juegos_titulo->first()->id;
                    $actualizarJuego = true;
                }

            }
            #existen mas de un titulo y un codigo: comprobar si algun titulo tiene ese codigo
            if ($titulo->count() > 1 && $idSony->count() == 1) {
                $temp_juegos_titulo = $titulo->where('id_sony', $consulta['codigo'])->where('plataforma', $consulta['consola'])->get();
                if ($temp_juegos_titulo->count() != 1) {
                    #Error de integridad
                    Log::error("route:Services/GuardarJuego.php> Error de integridad: {$consulta['titulo']} y {$consulta['consola']}");
                    dd("Error de integridad, existen mas de un titulo y un codigo");
                }
                else{
                    #se encontro la asociacion, se requiere actualizar el juego
                    $juegoExistente = $temp_juegos_titulo->first()->id;
                    $actualizarJuego = true;
                }
            }

            #existen mas de un titulo y mas de un codigo: problema de integridad
            if ($titulo->count() > 1 && $idSony->count() > 1) {
                Log::error("route:Services/GuardarJuego.php> Error de integridad: {$consulta['titulo']} y {$consulta['consola']}");
                dd("Error de integridad, existen mas de un titulo y mas de un codigo");
            }
        }

        
        #Existe el Titulo pero no el ID: Comparar, pues puede variar la consola
        if($titulo->isNotEmpty() && $idSony->isEmpty()) {
            #se espera encontrar a lo sumo dos juegos en este caso
            $temp_juegos_consola = $titulo->where('consola', $consulta['consola'])->get();
            if ($temp_juegos_consola->count() == 1) {
                #se encontro el juego
                $juegoExistente = $temp_juegos_consola->first()->id;
                $actualizarJuego = true;
            }
            else {
                #Error de integridad, 0 o 2 no son viables para evaluar
                Log::error("route:Services/GuardarJuego.php> Error de integridad: {$consulta['titulo']} y {$consulta['consola']}");
                dd("Error de integridad, Existe el titulo y no el ID");
            }
        }

        #No existe el titulo pero si el ID: aunque se compare la consola, no se garantiza que sea el juego
        if($titulo->isEmpty() && $idSony->isNotEmpty()) {
            #error de integridad, no hay datos para comparar este caso
            Log::error("route:Services/GuardarJuego.php> Error de integridad: {$consulta['titulo']} y ID {$idSony}");
            dd("Error de integridad, no existe el titulo pero si el ID, {$idSony}");
        }

        #No existe el Titulo ni el ID: El juego es nuevo y simplemente se agrega
        if ($titulo->isEmpty() && $idSony->isEmpty()) {
           // creacion del modelo y asignacion de relaciones
            Log::info("route:Services/GuardarJuego.php> Guardando juego: {$consulta['titulo']}");
            $juego->titulo = $consulta['titulo'];
            $juego->plataforma = $consulta['consola'];
            $juego->imgLowURL = str_replace("w=1024", 'w=256', $consulta['imagenURL']);
            $juego->imgURL = $consulta['imagenURL'];
            $juego->id_sony = $consulta['codigo'];
            $juego->oferta = $consulta['oferta'] ? 1 : 0;

            $responseJuego = $juego->save();
        }
        


        if($actualizarJuego) {
            $titulo = $titulo->firstWhere("consola", $consulta['consola']);

            // ya sea que el codigo o el titulo dierean verdad, hay que cambiar todo
            Log::info("route:Services/GuardarJuego.php> Actualizando juego: {$consulta['titulo']} e ID {$consulta['codigo']}");
            $responseJuego = $juegoExistente->update([
                'titulo' => $consulta['titulo'],
                'id_sony' => $consulta['codigo'],
                'plataforma' => $consulta['consola'],
                'imgLowURL' => str_replace("w=1024", 'w=256', $consulta['imagenURL']),
                'imgURL' => $consulta['imagenURL'],
                'oferta' => $consulta['oferta'] ? 1 : 0,
            ]);
            $juego = Juego::find($juegoExistente->id);   //para usar el mismo objeto en la siguiente parte
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
