<?php

namespace App\Jobs;

use App\Models\Juego;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CoordinadorActualizarJuego implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 120;

    protected $idsJuegos;
    protected $batchId;

    public function __construct(array $idsJuegos)
    {
        $this->idsJuegos = $idsJuegos;
        $this->batchId = Str::uuid()->toString();
    }

    public function handle(): void
    {
        $total = count($this->idsJuegos);

        Log::info("📦 Iniciando actualización de juegos", [
            'batch_id' => $this->batchId,
            'total_juegos' => $total
        ]);

        // Inicializar contadores en caché
        Cache::put("batch:{$this->batchId}:total", $total, now()->addDay());
        Cache::put("batch:{$this->batchId}:exitosos", 0, now()->addDay());
        Cache::put("batch:{$this->batchId}:fallidos", 0, now()->addDay());
        Cache::put("batch:{$this->batchId}:status", 'procesando', now()->addDay());

        // Despachar jobs individuales
        foreach ($this->idsJuegos as $index => $id) {
            ActualizarJuegoJob::dispatch($id, $this->batchId)
                ->delay(now()->addSeconds($index * 2)); // 2 segundos de delay entre cada job
        }

        Log::info("✅ Jobs despachados", [
            'batch_id' => $this->batchId,
            'total' => $total
        ]);
    }
}
