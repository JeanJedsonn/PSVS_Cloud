<?php

namespace App\Livewire;


use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

use App\Livewire\PartesTabla\ConBusqueda;
use App\Livewire\partesTabla\ConOrdenamiento;
use App\Livewire\partesTabla\ConPaginacion;
use App\Livewire\partesTabla\ConSeleccion;

use App\Services\ConstructorConsultasJuego;
use App\Services\ActualizadorJuegos;
use App\Jobs\CoordinadorImportacionJuegos;
use App\Jobs\CoordinadorActualizarJuego;
use App\Jobs\ExportarJuegosJob;

use App\Models\Juego;
use App\Models\JuegoMoneda;

//use App\Exports\JuegosSeleccionadosExport;
use App\Exports\JuegosExport;


use Symfony\Component\HttpFoundation\BinaryFileResponse;

class TablaJuego extends Component
{
    use WithPagination;
    use ConBusqueda;
    use ConOrdenamiento;    // odernar() | esOrdenamientoComplejo()
    use ConPaginacion;
    use ConSeleccion;

    public $mensajes = []; // Para debugging

    protected ConstructorConsultasJuego $queryBuilder;
    protected ActualizadorJuegos $actualizador;

    public function boot(ConstructorConsultasJuego $queryBuilder, ActualizadorJuegos $actualizador)
    {
        $this->queryBuilder = $queryBuilder;
        $this->actualizador = $actualizador;
    }

    public function render()
    {
        $query = $this->queryBuilder->construirQuery(
            $this->ordenarPor,
            $this->ordenDireccion,
            $this->busqueda
        );
        $resultados = $query->paginate($this->porPagina);

        return view('livewire.tablaJuego', compact('resultados'));
    }

    /**
     * Summary of exportarTodos
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function exportarTodos()
    {
        set_time_limit(600);
        $nombreArchivo = 'psvs_bd_' . now()->format('Y-m-d_His') . '.xlsx';

        session()->flash('success',
            'Se ha iniciado la exportación de todos los juegos'.
            'Tiempo maximo para la descarga: 10 min.'
        );

        return Excel::download(
            new JuegosExport([]),
            $nombreArchivo
        );
    }

    /**
     * Summary of exportarSeleccionados
     * @return BinaryFileResponse|null
     */
    public function exportarSeleccionados()
    {
        if (empty($this->seleccionados)) {
            session()->flash('error', 'No hay juegos seleccionados');
            return null;
        }

        $nombreArchivo = 'juegos_seleccionados_' . now()->format('Y-m-d_His') . '.xlsx';

        return Excel::download(
            new JuegosExport($this->seleccionados),
            $nombreArchivo
        );
    }

    public function exportarOfertas()
    {
        set_time_limit(300);

        // asignar las id si almenos hay una oferta
        $consulta = JuegoMoneda::where('precio_oferta', '!=', '-')->pluck('juego_id')->toArray();
        $oferta = [];
        foreach ($consulta as $id) {
            if (!in_array($id, $oferta)) {
                $oferta[] = $id;
            };
        }
        Log::info("route: Livewire/TablaJuego.php> exportarOfertas: ".json_encode($oferta));

        if (empty($oferta)) {
            session()->flash('error', 'No hay juegos en oferta');
            return null;
        }

        $nombreArchivo = 'juegos_seleccionados_' . now()->format('Y-m-d_His') . '.xlsx';

        session()->flash('success',
            'Se ha iniciado la exportación de ' . count($oferta) . ' juego(s)'.
            'Tiempo maximo para la descarga: 5min.'
        );

        return Excel::download(
            new JuegosExport($oferta),
            $nombreArchivo
        );
        //ExportarJuegosJob::dispatch($oferta);
    }


    /**
     * Summary of actualizarJuegoSeleccionado
     * @param mixed $juego
     * @return void
     */
    public function actualizarJuegoSeleccionado(int $juego):void
    {
        Log::info("route: Livewire/TablaJuego.php> Actualizando juego: {$juego})");
        $this->actualizador->actualizarJuego($juego);


        session()->flash('success',
            'Se ha completado la actualización'
        );
        $this->resetPage();
    }

    public function actualizarTodos()
    {
        // Despachar coordinador
        $ids = Juego::all()->pluck('id')->toArray();
        CoordinadorActualizarJuego::dispatch($ids);

        session()->flash('success',
            'Se ha iniciado la actualización de ' . count($ids) . ' juego(s). ' .
            'Cada juego se procesará individualmente en segundo plano.'
        );
    }

    public function actualizarSeleccionados()
    {
        if (empty($this->seleccionados)) {
            session()->flash('error', 'No hay juegos seleccionados');
            return;
        }

        // Despachar coordinador
        CoordinadorActualizarJuego::dispatch($this->seleccionados);

        session()->flash('success',
            'Se ha iniciado la actualización de ' . count($this->seleccionados) . ' juego(s). ' .
            'Cada juego se procesará individualmente en segundo plano.'
        );
    }

    public function eliminarSeleccionados()
    {
        if (empty($this->seleccionados)) {
            session()->flash('error', 'No hay juegos seleccionados');
            return;
        }

        $cantidad = count($this->seleccionados);
        Juego::destroy($this->seleccionados);
        
        $this->limpiarSeleccion();

        session()->flash('success', "Se han eliminado $cantidad juego(s) correctamente.");
    }

    /**
     * Importar juegos desde archivo CSV/Excel
     * PD1: El job se ejecutara en segundo plano
     * PD2: Esta funcion solo es para busquedas masivas
     */
    public function importarJuegos()
    {

        // Despachar job en segundo plano
        CoordinadorImportacionJuegos::dispatch();

        session()->flash('success', 'Se ha iniciado la importación del archivo en segundo plano');
    }

}


## Jerarquía de métodos en el componente:
/*
┌────────────────────────────────┐
│   Componente Tabla             │
│                                │
│   Métodos propios:             │
│   - render()                   │
│   - buscar()                   │
│   - ordenar()                  │
│   - accionMasiva()             │
│   - actualizarJuego()          │
│   - limpiarBusqueda()          │
│   - limpiarSeleccion()         │
│   - toggleTodos()              │
│   - exportarTodos()            │
│   - exportarSeleccionados()    │
│   - importarJuegos()           │
│                                │
│   Métodos de Traits:           │
│   ├─ ConBusqueda               │
│   │  - buscar()                │
│   │  - limpiarBusqueda()       │
│   │                            │
│   ├─ ConOrdenamiento           │
│   │  - ordenar()               │
│   │                            │
│   ├─ WithPagination            │
│   │  - nextPage()              │
│   │  - previousPage()          │
│   │  - gotoPage()              │
│   │  - resetPage()             │
│   │                            │
│   └─ ConSeleccion              │
│      - toggleTodos()           │
│      - limpiarSeleccion()      │
└────────────────────────────────┘

## Jerarquía de carpetas:
resources/views/livewire/
├── tabla.blade.php                         ---> Coordinador
└── tabla/
    ├── barra-acciones.blade.php                ---> button_1:click,  button_2:click, button_3:click
    ├── barra-busqueda.blade.php                ---> form:[input_1:text , button_1:submit, button_2:button ]
    ├── controles-paginacion.blade.php          ---> Select
    ├── paginacion.blade.php                    ---> button_1:click, button_2:click, button_3:click, button_4:click,
    |                                                button_5:click, button_6:click
    ├── tabla-contenido.blade.php               ---> thead:[th wire:click] tbody:[td con datos]
    └── partials/
        ├── fila-juego.blade.php
        └── icono-orden.blade.php
*/
