<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Log;
use App\Exports\JuegosExport;

class ExportarJuegosJob implements ShouldQueue
{
    use Queueable;
    public $timeout = 300;
    protected array $juegoIDs = [];
    /**
     * Create a new job instance.
     */
    public function __construct(array $juegoIDs)
    {
        Log::info(json_encode($juegoIDs));
        $this->juegoIDs = $juegoIDs;
        Log::info('Exportando juegos ');
    }

    /**
     * Execute the job.
     */
    public function handle()
    {

        $nombreArchivo = 'juegos_oferta_' . now()->format('Y-m-d_His') . '.xlsx';

        Excel::store(
            new JuegosExport($this->juegoIDs),
            "public/",

        );
    }
}
