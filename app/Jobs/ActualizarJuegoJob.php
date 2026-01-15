<?php

namespace App\Jobs;

use App\Models\Juego;

use App\Services\ActualizadorJuegos;

use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class ActualizarJuegoJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Batchable;

    public $timeout = 60; // 1 minuto por juego
    public $tries = 3;
    public $backoff = [10, 30, 60]; // Reintentos con delays progresivos

    protected $juegoId;
    //protected $batchId; // Para tracking del lote

    public function __construct(int $juegoId, ?string $batchId = null)
    {
        $this->juegoId = $juegoId;
        $this->batchId = $batchId;
        Log::info("📦 Iniciando actualización de juego: {$juegoId}");
    }

    public function handle(ActualizadorJuegos $actualizador): void
    {
        $juego = Juego::find($this->juegoId);

        if (!$juego) {
            Log::warning("Juego no encontrado: {$this->juegoId}");
            return;
        }

        Log::info("Actualizando juego: {$juego->titulo} (ID: {$this->juegoId})");

        $exito = $actualizador->actualizarJuego($juego->id);

        if ($exito) {
            $this->incrementarContador('exitosos');
            Log::info("✅ Juego actualizado exitosamente: {$juego->titulo}");
        } else {
            $this->incrementarContador('fallidos');
            Log::error("❌ Falló actualización de: {$juego->titulo}");
        }
    }

    public function failed(\Throwable $exception): void
    {
        $this->incrementarContador('fallidos');
        Log::error("Error fatal actualizando juego {$this->juegoId}: " . $exception->getMessage());
    }

    /**
     * Incrementar contador de progreso en caché
     */
    protected function incrementarContador(string $tipo): void
    {
        if ($this->batchId) {
            $nuevo = Cache::increment("batch:{$this->batchId}:{$tipo}");

            // Verificar si terminaron todos
            $total = Cache::get("batch:{$this->batchId}:total", 0);
            $exitosos = Cache::get("batch:{$this->batchId}:exitosos", 0);
            $fallidos = Cache::get("batch:{$this->batchId}:fallidos", 0);
            $procesados = $exitosos + $fallidos;

            if ($procesados >= $total) {
                Cache::put("batch:{$this->batchId}:status", 'completado', now()->addDay());
                Cache::put("batch:{$this->batchId}:completado_en", now(), now()->addDay());

                Log::info("🎉 Todos los jobs completados", [
                    'batch_id' => $this->batchId,
                    'exitosos' => $exitosos,
                    'fallidos' => $fallidos,
                    'total' => $total,
                ]);
            }
        }
    }
}
