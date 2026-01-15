<div class="mb-4">
    <!-- Toolbar -->
    <div class="row g-3 align-items-center justify-content-between mb-4">



    </div>

    <!-- Barra de acciones -->
    <div class="card card-body shadow-sm border-0 mb-4">
        <div class="d-flex flex-wrap gap-2 justify-content-center justify-content-md-start">
            <button wire:click="exportarTodos" class="btn btn-primary d-flex align-items-center gap-2">
                <span>📥</span> Exportar Todos
            </button>
            
            <button wire:click="exportarOfertas" class="btn btn-warning text-white d-flex align-items-center gap-2">
                <span>🏷️</span> Exportar Ofertas
            </button>
            
            <button wire:click="exportarSeleccionados" class="btn btn-success d-flex align-items-center gap-2">
                <span>📥</span> Exportar Seleccionados
            </button>

            <button wire:click="eliminarSeleccionados" 
                    wire:confirm="¿Estás seguro de que deseas eliminar los juegos seleccionados? Esta acción no se puede deshacer."
                    class="btn btn-danger d-flex align-items-center gap-2">
                <span>🗑️</span> Eliminar Seleccionados
            </button>
            
            <div class="vr mx-2 d-none d-md-block"></div>
            
            <button wire:click="actualizarTodos" class="btn btn-outline-primary">
                🔃 Actualizar Todos
            </button>
            
            <button wire:click="actualizarSeleccionados" class="btn btn-outline-success">
                🔃 Actualizar Seleccionados
            </button>
        </div>
    </div>

    <!-- Mensajes Flash -->
    <div>
        @if (session()->has('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session()->has('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
    </div>

    
    <!-- Filtros y busqueda -->
    <div class="d-flex flex-wrap align-items-center gap-3 mb-4 p-3 bg-white rounded shadow-sm">
        
        <!-- Filtros de Ordenamiento -->
        <span class="fw-bold text-secondary">Ordenar por:</span>
        <div class="btn-group" role="group">
            <button wire:click="ordenar('titulo')" type="button" class="btn {{ $ordenarPor === 'titulo' ? 'btn-primary' : 'btn-outline-secondary' }}">
                Título @if($ordenarPor === 'titulo') {{ $ordenDireccion === 'asc' ? '▼' : '▲' }} @endif
            </button>
            <button wire:click="ordenar('tiene_oferta')" type="button" class="btn {{ $ordenarPor === 'tiene_oferta' ? 'btn-primary' : 'btn-outline-secondary' }}">
                Oferta @if($ordenarPor === 'tiene_oferta') {{ $ordenDireccion === 'asc' ? '▼' : '▲' }} @endif
            </button>
            <button wire:click="ordenar('plataforma')" type="button" class="btn {{ $ordenarPor === 'plataforma' ? 'btn-primary' : 'btn-outline-secondary' }}">
                Consola @if($ordenarPor === 'plataforma') {{ $ordenDireccion === 'asc' ? '▼' : '▲' }} @endif
            </button>
        </div>

        <!-- Selector de filas -->
        <div class="col-auto">
            <div class="d-flex align-items-center gap-2">
                <span class="fw-bold text-secondary">Mostrar:</span>
                <select wire:model.live="porPagina" class="form-select w-auto">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
            </div>
        </div>

        <!-- Botón de importar -->
        <div class="d-flex gap-2 ms-md-auto">
            <button wire:click="importarJuegos" class="btn btn-purple text-white shadow-sm text-nowrap" style="background-color: #6f42c1;">
                📤 Importar Juegos
            </button>
        </div>

        <!-- Barra de búsqueda -->
        <div class="col-12 col-md-auto">
            <form wire:submit.prevent="buscar" class="d-flex gap-2">
                <div class="input-group">
                    
                    <input type="text" wire:model="busquedaInput" class="form-control" placeholder="Buscar por título..." style="min-width: 250px;">
                    <button type="submit" class="input-group-text btn btn-primary">🔍</button>
                </div>
            </form>
        </div>

    </div>

    <!-- Grid de Juegos -->
    {{-- <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-xl-4 g-4 mb-5"></div> --}}
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 20px;">
        @forelse ($resultados as $juego)
            <div class="col" wire:key="juego-{{ $juego->id }}">
                <x-game-card :cover="$juego->imgLowURL" :title="$juego->titulo" :platform="$juego->plataforma">
                    {{-- Checkbox superpuesto --}}
                    <x-slot:topLeft>
                        <div class="rounded">
                            <input type="checkbox" value="{{ $juego->id }}" wire:model="seleccionados" class="form-check-input m-0" style="cursor: pointer; transform: scale(1.2);">
                        </div>
                    </x-slot:topLeft>

                    {{-- Lista de precios --}}
                    <x-slot:slot>
                        <div class="list-group list-group-flush small">
                            @foreach ($juego->juegoMonedas as $moneda)
                                <div class="list-group-item d-flex justify-content-between align-items-center px-0 py-1 border-0">
                                    <a href="{{ $moneda->link }}" target="_blank" class="text-decoration-none fw-medium">
                                        {{ $moneda->moneda->region }}
                                    </a>
                                    <div class="text-end lh-1">
                                        @if($moneda->precio_oferta !== '-' && $moneda->precio_oferta !== 'N/A')
                                            <div class="text-muted text-decoration-line-through" style="font-size: 0.85em;">
                                                {{ $moneda->precio_original }}
                                            </div>
                                            <div class="text-success fw-bold">
                                                {{ $moneda->precio_oferta }}
                                            </div>
                                        @else
                                            <div class="fw-bold text-dark">
                                                {{ $moneda->precio_original }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </x-slot:slot>

                    {{-- Acciones --}}
                    <x-slot:actions>
                        <form wire:submit="actualizarJuegoSeleccionado('{{$juego->id}}')" class="d-inline">
                            <button type="submit" class="btn btn-sm btn-outline-primary" title="Actualizar">
                                ↺
                            </button>
                        </form>

                        <form action="{{ route('juegos.destroy', $juego->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger" 
                                onclick="return confirm('¿Estás seguro de eliminar este juego?')" title="Eliminar">
                                🗑️
                            </button>
                        </form>
                    </x-slot:actions>
                </x-game-card>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info text-center py-5">
                    @if($busqueda)
                        <h4 class="alert-heading">Sin resultados</h4>
                        <p>No se encontraron juegos con el título "{{ $busqueda }}"</p>
                    @else
                        <h4 class="alert-heading">No hay juegos</h4>
                        <p>No hay juegos disponibles en la base de datos.</p>
                    @endif
                </div>
            </div>
        @endforelse
    </div>

    <!-- Paginación -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3 border-top pt-4">
        <div class="text-muted small">
            @if($resultados->total() > 0)
                Mostrando <strong>{{ $resultados->firstItem() }}</strong> a <strong>{{ $resultados->lastItem() }}</strong> de <strong>{{ $resultados->total() }}</strong> resultados
            @else
                No hay resultados
            @endif
        </div>

        <div class="d-flex gap-1 overflow-auto pb-2">
            {{-- Botones de Paginación Customizados para Bootstrap --}}
            @if (!$resultados->onFirstPage())
                <button wire:click="gotoPage(1)" class="btn btn-sm btn-outline-secondary" title="Inicio">«</button>
                <button wire:click="previousPage" class="btn btn-sm btn-outline-secondary">‹</button>
            @endif

            {{-- Lógica simplificada de rango de páginas --}}
            @php
                $paginaActual = $resultados->currentPage();
                $totalPaginas = $resultados->lastPage();
                $inicio = max(1, $paginaActual - 2);
                $fin = min($totalPaginas, $paginaActual + 2);
            @endphp

            @for($i = $inicio; $i <= $fin; $i++)
                <button wire:click="gotoPage({{ $i }})" class="btn btn-sm {{ $i == $paginaActual ? 'btn-primary' : 'btn-outline-secondary' }}">
                    {{ $i }}
                </button>
            @endfor

            @if ($resultados->hasMorePages())
                <button wire:click="nextPage" class="btn btn-sm btn-outline-secondary">›</button>
                <button wire:click="gotoPage({{ $totalPaginas }})" class="btn btn-sm btn-outline-secondary" title="Final">»</button>
            @endif
        </div>
    </div>
</div>