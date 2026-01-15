<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use League\Csv\Reader;

class CoordinadorImportacionJuegos implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 300; // 5 minutos para leer y despachar
    protected $batchId;

    public function __construct()
    {
        $this->batchId = Str::uuid()->toString();
    }

    public function handle(): void
    {
        $rutaArchivo = storage_path('app/public/dato.csv');

        if (!file_exists($rutaArchivo)) {
            Log::error("❌ Archivo CSV no encontrado en: {$rutaArchivo}");
            return;
        }

        try {
            $csv = Reader::createFromPath($rutaArchivo, 'r');
            $csv->setHeaderOffset(0);

            $registros = iterator_to_array($csv); // Cargar en memoria para contar
            $total = count($registros);

            Log::info("📦 Iniciando importación masiva de {$total} juegos", ['batch_id' => $this->batchId]);

            // Inicializar contadores en caché
            Cache::put("batch:{$this->batchId}:total", $total, now()->addDay());
            Cache::put("batch:{$this->batchId}:exitosos", 0, now()->addDay());
            Cache::put("batch:{$this->batchId}:fallidos", 0, now()->addDay());
            Cache::put("batch:{$this->batchId}:status", 'procesando', now()->addDay());

            foreach ($registros as $index => $fila) {
                if (empty($fila['Juegos'])) {
                    // Si la fila está vacía o no tiene el campo Juegos, saltar pero reducir el total esperado
                     Cache::decrement("batch:{$this->batchId}:total");
                     continue;
                }

                ImportarJuegoJob::dispatch($fila['Juegos'], $this->batchId)
                    ->delay(now()->addSeconds($index * 1)); // 1 segundo de delay entre jobs para no saturar
            }

            Log::info("✅ Todos los jobs de importación han sido despachados");

        } catch (\Exception $e) {
            Log::error("Error leyendo CSV o despachando jobs: " . $e->getMessage());
        }
    }
}
