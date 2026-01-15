<?php

namespace App\Jobs;

use App\Services\BuscadorJuego;
use App\Services\GuardarJuego;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ImportarJuegoJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Batchable;

    public $timeout = 60; // 1 minuto por juego
    public $tries = 3;

    protected $titulo;
    protected $minimaCoincidencia = 95;

    public function __construct(string $titulo, string $batchId)
    {
        $this->titulo = $titulo;
        $this->batchId = $batchId;
    }

    public function handle(BuscadorJuego $buscador, GuardarJuego $guardador): void
    {
        $tituloLimpio = str_replace(" Abrir en EEUU", "", $this->titulo);
        
        $tituloLimpio = str_replace("™", "", $tituloLimpio);
        $tituloLimpio = str_replace("®", "", $tituloLimpio);
        $tituloLimpio = str_replace("TM", "", $tituloLimpio);
        $tituloLimpio = str_replace("\"", "", $tituloLimpio);
        $tituloLimpio = str_replace("'", "", $tituloLimpio);
        $tituloLimpio = str_replace("´", "", $tituloLimpio);
        $tituloLimpio = str_replace("'", "", $tituloLimpio);
        $tituloLimpio = trim(preg_replace('/\s+/', ' ', $tituloLimpio));


        Log::info("🔍 Buscando juego: {$tituloLimpio} (Batch: {$this->batchId})");

        try {
            $consulta = $buscador->buscar($tituloLimpio);
            $juegoEncontrado = null;
            $porcentaje = 0;

            foreach ($consulta as $juego) {
                similar_text($juego["titulo"], $tituloLimpio, $porcentaje);
                $porcentaje = round($porcentaje, 2);

                if ($porcentaje > $this->minimaCoincidencia) {
                    $juegoEncontrado = $juego;
                    break;
                }
            }

            if ($juegoEncontrado) {
                $response = $guardador->guardar($juegoEncontrado);
                $this->incrementarContador('exitosos');
                Log::info("✅ Juego guardado: {$tituloLimpio}");
            } else {
                $this->incrementarContador('fallidos');
                Log::warning("⚠️ Juego NO encontrado: {$tituloLimpio}");
            }

        } catch (\Exception $e) {
            $this->incrementarContador('fallidos');
            Log::error("❌ Error importando '{$tituloLimpio}': " . $e->getMessage());
        }
    }

    protected function incrementarContador(string $tipo): void
    {
        if ($this->batchId) {
            Cache::increment("batch:{$this->batchId}:{$tipo}");

            $total = Cache::get("batch:{$this->batchId}:total", 0);
            $exitosos = Cache::get("batch:{$this->batchId}:exitosos", 0);
            $fallidos = Cache::get("batch:{$this->batchId}:fallidos", 0);
            $procesados = $exitosos + $fallidos;

            if ($procesados >= $total) {
                Cache::put("batch:{$this->batchId}:status", 'completado', now()->addDay());
                Cache::put("batch:{$this->batchId}:completado_en", now(), now()->addDay());

                Log::info("🎉 Importación masiva completada", [
                    'batch_id' => $this->batchId,
                    'exitosos' => $exitosos,
                    'fallidos' => $fallidos,
                    'total' => $total,
                ]);
            }
        }
    }
}
