<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Pool;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ConnectException;
use Symfony\Component\DomCrawler\Crawler;
use App\Models\Moneda;
use Illuminate\Support\Facades\Log;

class BuscadorJuego
{
    private $concurrencia = 4; // Número de requests simultáneos (verificado para 4)

    /**
     * Procesa el HTML y extrae los datos de los juegos
     */
    private function procesarHTML($html, $region, $titulo): array
    {

        // Verificar si el HTML contiene datos válidos
        if (empty($html)) {
            Log::warning("HTML vacío para {$titulo} en {$region}");
            return [];
        }

        // creacion del crawler
        $crawler = new Crawler($html);

        // Verificar si se encontraron resultados
        if ($crawler->filter(".psw-grid-list li")->count() === 0) {
            Log::info("No se encontraron resultados para {$titulo} en {$region}");
            return [];
        }

        $listaGames = $crawler->filter(".psw-grid-list li")->slice(0, 24);
        $retornar = [];

        foreach ($listaGames as $listGame) {
            try {
                $gameCrawler = new Crawler($listGame);
                $tituloJuego = trim($gameCrawler->filter(".psw-t-body")->text(""));

                if (empty($tituloJuego)) {
                    continue;
                }

                $precioActual = $gameCrawler->filter("div.psw-fill-x.psw-price.psw-l-inline.psw-l-line-left-top > div > span.psw-m-r-3")->text("");
                $precioOriginal = $gameCrawler->filter(".psw-l-inline.psw-l-line-left-top > div > s")->text("");

                $consolasList = $gameCrawler->filter("span.psw-on-graphic");
                $imagen = $gameCrawler->filter("span > img")->attr("src");
                $imagen = str_replace("thumb=true", "", $imagen);
                $imagen = str_replace("w=54", 'w=1024', $imagen);

                $consolas = "";
                $consolasList->each(function (Crawler $consola) use (&$consolas) {
                    $consolas .= $consola->text("");
                });

                $link = $gameCrawler->filter("div.psw-product-tile.psw-interactive-root > a")->attr("href");

                if (!str_contains($link, "concept")) {
                    $codigo = explode("-", $link);
                    $codigo = isset($codigo[3]) ? $codigo[3] : null;
                } else {
                    continue;
                }

                if (empty($consolas)) {
                    if (str_contains($tituloJuego, "PS4") && str_contains($tituloJuego, "PS5")) {
                        $consolas = "PS4 & PS5";
                    } else if (str_contains($tituloJuego, "PS4")) {
                        $consolas = "PS4";
                    } else if (str_contains($tituloJuego, "PS5")) {
                        $consolas = "PS5";
                    } else {
                        $consolas = "No especificado";
                    }
                } else {
                    if ($consolas == "PS4PS5" || $consolas == "PS5PS4") {
                        $consolas = "PS4 & PS5";
                    }
                }

                $tituloJuego = str_replace("™", "", $tituloJuego);
                $tituloJuego = str_replace("®", "", $tituloJuego);
                //$tituloJuego = str_replace("TM", "", $tituloJuego);
                $tituloJuego = str_replace("\"", "", $tituloJuego);
                $tituloJuego = str_replace("'", "", $tituloJuego);
                $tituloJuego = str_replace("´", "", $tituloJuego);
                $tituloJuego = str_replace("'", "", $tituloJuego);
                $tituloJuego = trim(preg_replace('/\s+/', ' ', $tituloJuego));
                
                $oferta = true;
                //si precio original esta vacio, no hay oferta
                if (empty($precioOriginal)) {
                    $precioOriginal = $precioActual;
                    $precioActual = "-";
                    $oferta = false;
                }

                $retornar[$codigo] = [
                    'titulo' => $tituloJuego,
                    'consola' => $consolas,
                    'precioOriginal' => $precioOriginal,
                    'precioActual' => $precioActual,
                    'link' => "https://store.playstation.com" . $link,
                    'imagenURL' => $imagen,
                    'codigo' => $codigo,
                    'oferta' => $oferta,
                ];

            } catch (\Exception $e) {
                Log::warning("Error procesando juego individual en {$region}: " . $e->getMessage());
                continue;
            }
        }

        return $retornar;
    }

    /**
     * Controlador de peticiones asincronas
     */
    private function extraerDatosJuegoConcurrente($client, $regiones, $titulo): array
    {
        $listaRegion = [];

        // Crear generador de requests
        $requests = function ($regiones, $titulo) {
            // Recorrer las regiones
            foreach ($regiones as $region) {
                $url = "https://store.playstation.com/" . $region . "/search/" . $titulo;
                // Crear request
                yield $region => new Request('GET', $url, [
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36'
                ]);
            }
        };

        // Configurar el pool (se usa cuando no se sabe la cantidad de peticiones asincronas)
        $pool = new Pool($client, $requests($regiones, $titulo), [
            // cantidad de peticiones que se realizaran
            'concurrency' => $this->concurrencia,
            // esto se ejecuta en cada response completado
            'fulfilled' => function ($response, $region) use (&$listaRegion, $titulo) {
                try {
                    Log::info("Response recibida para región: {$region}");

                    // Verificar si el HTML contiene datos válidos (html 200: OK :D)
                    if ($response->getStatusCode() === 200) {
                        $html = $response->getBody()->getContents();
                        $juegos = $this->procesarHTML($html, $region, $titulo);

                        if (!empty($juegos)) {
                            $listaRegion[$region] = $juegos;
                            Log::info("Éxito en región {$region}: " . count($juegos) . " juegos encontrados");
                        } else {
                            $listaRegion[$region] = [];
                        }
                    } else {
                        Log::warning("Status code no esperado: " . $response->getStatusCode() . " para {$titulo} en {$region}");
                        $listaRegion[$region] = [];
                    }
                } catch (\Exception $e) {
                    Log::error("Error procesando respuesta para {$region}: " . $e->getMessage());
                    $listaRegion[$region] = [];
                }
            },
            // esto se ejecuta cuando falla
            'rejected' => function ($reason, $region) use (&$listaRegion) {
                Log::error("Request fallida para región {$region}: " . $reason->getMessage());
                $listaRegion[$region] = [];
            },
        ]);

        // Iniciar el pool y esperar a que termine
        $promise = $pool->promise();
        $promise->wait();

        return $listaRegion;
    }

    /**
     * Retorna una tupla con los resultados de la búsqueda
     * @param string $titulo Título del juego
     * @return array Devuelve un `array` con los datos del juego
     */
    public function buscar($titulo)
    {
        try {
            $REGIONES = Moneda::all()->pluck('direccion')->toArray();

            if (empty($REGIONES)) {
                Log::error("No se encontraron regiones configuradas");
                return ['error' => 'No hay regiones configuradas'];
            }

            $clientConfig = [
                'timeout' => 30,
                'connect_timeout' => 10,
            ];

            $client = new Client($clientConfig);

            Log::info("Iniciando búsqueda concurrente en " . count($REGIONES) . " regiones");

            // Extraer datos de forma concurrente
            $listaRegion = $this->extraerDatosJuegoConcurrente($client, $REGIONES, $titulo);
            // Verificar si tenemos datos de la región USA
            if (!isset($listaRegion["en-us"]) || empty($listaRegion["en-us"])) {
                Log::error("No se encontraron datos para la región base (en-us)");
                return ['error' => 'No se pudieron obtener datos de la región base (en-us)'];
            }

            $retornar = [];

            // Recorre la lista de juegos en region USA
            /*  listaRegion=["en-us"] los indices son regiones
                listaRegion["en-us"]: ["codigoUSA1"]=>{"codigo":"","titulo":"","precioOriginal":"","precioActual":"","link":""}] 
                cada indice en en una region contiene el prototipo del objeto juego
                
                retornar["codigoUSA1"] tiene la lista de juegos pero usa el codigo USA como indice    
            */

            // recorre todos los juegos que se encontraron en region USA
            foreach ($listaRegion["en-us"] as $codigoJuegoUSA => $juego) {

                //se usa a USA como base
                $retornar[$codigoJuegoUSA] = $juego;
                $retornar[$codigoJuegoUSA]["precioOriginal"] = [];
                $retornar[$codigoJuegoUSA]["precioActual"] = [];
                $retornar[$codigoJuegoUSA]["link"] = [];


                // Recorre la lista de juegos en todas las regiones, incluyendo USA
                foreach ($listaRegion as $region => $juegos) {
                    
                    if (empty($juegos)) {
                        continue;
                    }

                    // Búsqueda directa mediante el código
                    if (isset($juegos[$codigoJuegoUSA])) {
                        $retornar[$codigoJuegoUSA]["precioOriginal"][$region] = $juegos[$codigoJuegoUSA]["precioOriginal"];
                        $retornar[$codigoJuegoUSA]["precioActual"][$region] = $juegos[$codigoJuegoUSA]["precioActual"];
                        $retornar[$codigoJuegoUSA]["link"][$region] = $juegos[$codigoJuegoUSA]["link"];

                        if (!$retornar[$codigoJuegoUSA] && $juegos[$codigoJuegoUSA]["oferta"]) {
                            $retornar[$codigoJuegoUSA]['oferta'] = true;
                        }
                    } else {
                        // Búsqueda mediante el título

                        $encontrado = false;
                        foreach ($juegos as $juego) {
                            similar_text($juego["titulo"], $retornar[$codigoJuegoUSA]["titulo"], $porcentaje);
                            $porcentaje = round($porcentaje, 2);
                            if ($porcentaje > 95) {
                                $retornar[$codigoJuegoUSA]["precioOriginal"][$region] = $juego["precioOriginal"];
                                $retornar[$codigoJuegoUSA]["precioActual"][$region] = $juego["precioActual"];
                                $retornar[$codigoJuegoUSA]["link"][$region] = $juego["link"];

                                if (!$retornar[$codigoJuegoUSA] && $juegos[$codigoJuegoUSA]["oferta"]) {
                                    $retornar[$codigoJuegoUSA]['oferta'] = true;
                                }
                                $encontrado = true;
                                Log::alert("Juego encontrado por título {$juego["titulo"]} contra {$retornar[$codigoJuegoUSA]["titulo"]}");
                                break;
                            }
                        }

                        // si no se encontro el juego, se rellena con vacio
                        if (!$encontrado) {
                            $retornar[$codigoJuegoUSA]["precioOriginal"][$region] = "N/A";
                            $retornar[$codigoJuegoUSA]["precioActual"][$region] = "N/A";
                            $retornar[$codigoJuegoUSA]["link"][$region] = "N/A";
                        }
                    }
                }
            }
            Log::info("Búsqueda completada. Total de juegos encontrados: " . count($retornar));
            return $retornar;

        } catch (\Exception $e) {
            Log::error("Error general en servicio BuscadorJuego: " . $e->getMessage());
            return ['error' => 'Error interno del servidor: ' . $e->getMessage()];
        }
    }

    public function buscar_ori($titulo)
    {
        try {
            $REGIONES = Moneda::all()->pluck('direccion')->toArray();

            if (empty($REGIONES)) {
                Log::error("No se encontraron regiones configuradas");
                return ['error' => 'No hay regiones configuradas'];
            }

            $clientConfig = [
                'timeout' => 30,
                'connect_timeout' => 10,
            ];

            $client = new Client($clientConfig);

            Log::info("Iniciando búsqueda concurrente en " . count($REGIONES) . " regiones");

            // Extraer datos de forma concurrente
            $listaRegion = $this->extraerDatosJuegoConcurrente($client, $REGIONES, $titulo);

            // Verificar si tenemos datos de la región USA
            if (!isset($listaRegion["en-us"]) || empty($listaRegion["en-us"])) {
                Log::error("No se encontraron datos para la región base (en-us)");
                return ['error' => 'No se pudieron obtener datos de la región base (en-us)'];
            }

            $retornar = [];

            // Recorre la lista de juegos en region USA
            foreach ($listaRegion["en-us"] as $codigoJuegoUSA => $juego) {
                $retornar[$codigoJuegoUSA] = $juego;
                $retornar[$codigoJuegoUSA]["precioOriginal"] = [];
                $retornar[$codigoJuegoUSA]["precioActual"] = [];
                $retornar[$codigoJuegoUSA]["link"] = [];
                //$retornar[$codigoJuegoUSA]["oferta"] = false;

                // Recorre la lista de juegos en todas las regiones
                foreach ($listaRegion as $region => $juegos) {
                    if (empty($juegos)) {
                        continue;
                    }

                    // Búsqueda directa mediante el código
                    if (isset($juegos[$codigoJuegoUSA])) {
                        $retornar[$codigoJuegoUSA]["precioOriginal"][$region] = $juegos[$codigoJuegoUSA]["precioOriginal"];
                        $retornar[$codigoJuegoUSA]["precioActual"][$region] = $juegos[$codigoJuegoUSA]["precioActual"];
                        $retornar[$codigoJuegoUSA]["link"][$region] = $juegos[$codigoJuegoUSA]["link"];

                        if (!$retornar[$codigoJuegoUSA] && $juegos[$codigoJuegoUSA]["oferta"]) {
                            $retornar[$codigoJuegoUSA]['oferta'] = true;
                        }
                    } else {
                        // Búsqueda mediante el título
                        foreach ($juegos as $juego) {
                            similar_text($juego["titulo"], $retornar[$codigoJuegoUSA]["titulo"], $porcentaje);
                            $porcentaje = round($porcentaje, 2);
                            if ($porcentaje > 95) {
                                $retornar[$codigoJuegoUSA]["precioOriginal"][$region] = $juego["precioOriginal"];
                                $retornar[$codigoJuegoUSA]["precioActual"][$region] = $juego["precioActual"];
                                $retornar[$codigoJuegoUSA]["link"][$region] = $juego["link"];

                                if (!$retornar[$codigoJuegoUSA] && $juegos[$codigoJuegoUSA]["oferta"]) {
                                    $retornar[$codigoJuegoUSA]['oferta'] = true;
                                }

                                Log::alert("Juego encontrado por título {$juego["titulo"]} contra {$retornar[$codigoJuegoUSA]["titulo"]}");
                                break;
                            }
                        }
                    }
                }
            }
            Log::info("Búsqueda completada. Total de juegos encontrados: " . count($retornar));
            return $retornar;

        } catch (\Exception $e) {
            Log::error("Error general en servicio BuscadorJuego: " . $e->getMessage());
            return ['error' => 'Error interno del servidor: ' . $e->getMessage()];
        }
    }

    /**
     * Configura el número de requests concurrentes
     */
    public function setConcurrencia(int $concurrencia)
    {
        $this->concurrencia = $concurrencia;
    }
}
