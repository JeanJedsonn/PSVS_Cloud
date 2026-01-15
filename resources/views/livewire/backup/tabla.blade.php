<div>

    <!--un indicador más sutil solo en la tabla -->
    <div wire:loading.delay style="padding: 10px; background: #fff3cd; border: 1px solid #ffc107; margin-bottom: 10px;">
        ⏳ Cargando datos...
    </div>

    <!-- Barra superior con selector y acciones -->
    <div style="margin-bottom: 15px; display: flex; justify-content: space-between; align-items: center;">
        <!-- Selector de filas por página -->
        <div>
            <label style="display: flex; align-items: center; gap: 10px;">
                <span>Mostrar:</span>

                {{--
                Selector
                    se usa wire:model.live para que se actualice en tiempo real (causa una consulta a la bd)
                    option value="10" se le asigna la VARIABLE del componente (porPagina)
                --}}
                <select wire:model.live="porPagina" style="padding: 5px 10px; border: 1px solid #ddd; border-radius: 3px; cursor: pointer;">
                    <option value="5">5</option>
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
                <span>filas</span>
            </label>
        </div>
    </div>


<!-- Barra de búsqueda -->
    <div style="margin-bottom: 10px;">
        <div style="display: flex; gap: 10px; align-items: center;">
            <form wire:submit.prevent="buscar" style="display: flex; gap: 10px; align-items: center;">
                <input
                    type="text"
                    wire:model="busquedaInput"
                    placeholder="Buscar por título..."
                    style="flex: 1; padding: 8px 12px; border: 1px solid #ddd; border-radius: 3px; font-size: 14px;"
                >
                <button
                    type="submit"
                    style="padding: 8px 20px; background: #007bff; color: white; border: none; border-radius: 3px; cursor: pointer; font-weight: 500;"
                >
                    🔍 Buscar
                </button>
            </form>
            {{--
            <input
                type="text"
                wire:model.live.debounce.300ms="busqueda"
                placeholder="Buscar por título..."
                style="flex: 1; padding: 8px 12px; border: 1px solid #ddd; border-radius: 3px; font-size: 14px;"
            >--}}
            <button
                wire:click="limpiarBusqueda"
                style="padding: 8px 15px; background: #6c757d; color: white; border: none; border-radius: 3px; cursor: pointer;"
                title="Limpiar búsqueda"
            >
                ✕ Limpiar
            </button>
        </div>
    </div>

    <!-- Barra de acciones masivas -->
    <div>
        <button wire:click="accionMasiva" style="margin-left: 10px; padding: 5px 15px; background: #2196f3; color: white; border: none; cursor: pointer; border-radius: 3px;">
            Exportar Seleccionados
        </button>
        <button wire:click="accionMasiva" style="margin-left: 10px; padding: 5px 15px; background: #2196f3; color: white; border: none; cursor: pointer; border-radius: 3px;">
            Exportar Todos
        </button>
    </div>



    @foreach ($mensajes as $msg)
        <p>Mensaje: {{ $msg }}</p>
    @endforeach

    <table border="1" cellpadding="5" style="width: 100%;">
        <thead>
            <tr>
                <th style="width: 40px;">
                    {{--
                    <input
                        type="checkbox"
                        wire:click="toggleTodos"
                        title="Seleccionar todos en esta página"
                    >--}}
                    Seleccionar
                    <!-- Agregar boton para expotar un excel con todos los juegos -->
                </th>

                <!-- Cabecera ordenable: Título -->
                <th wire:click="ordenar('titulo')" style="cursor: pointer; user-select: none;">
                    Título
                    @if($ordenarPor === 'titulo')
                        @if($ordenDireccion === 'asc')
                            ▲
                        @else
                            ▼
                        @endif
                    @else
                        <span style="opacity: 0.3;">▲▼</span>
                    @endif
                </th>
                <th>Links</th>
                <th>Precio Estandar</th>
                <!-- th>Oferta</th -->

                <!-- Cabecera ordenable: Oferta -->
                <th wire:click="ordenar('tiene_oferta')" style="cursor: pointer; user-select: none;">
                    Oferta
                    @if($ordenarPor === 'tiene_oferta')
                        @if($ordenDireccion === 'asc')
                            ▲
                        @else
                            ▼
                        @endif
                    @else
                        <span style="opacity: 0.3;">▲▼</span>
                    @endif
                </th>

                <!-- Cabecera ordenable: Consola -->
                <th wire:click="ordenar('plataforma')" style="cursor: pointer; user-select: none;">
                    Consola
                    @if($ordenarPor === 'plataforma')
                        @if($ordenDireccion === 'asc')
                            ▲
                        @else
                            ▼
                        @endif
                    @else
                        <span style="opacity: 0.3;">▲▼</span>
                    @endif
                </th>

                <th>Imagen</th>
                <th>Acciones</th>
                <th>Eliminar</th>
            </tr>
        </thead>

        <tbody>
            @foreach($resultados as $juego)
                {{--tr wire:key se le asigna un identificador unico a cada fila de la tabla--}}
                <tr wire:key="{{ $juego->id }}">
                    <!-- Checkbox -->
                    {{--
                    input type="checkbox" se le asigna un identificador unico a cada checkbox
                    value se le asigna el id del juego
                    wire:model.live se le asigna la VARIABLE del componente
                    nota: live es para que se actualice en tiempo real, si se deselecciona el checkbox se elimina el id
                    --}}
                    <td style="text-align: center;">
                        <input
                            type="checkbox"
                            value="{{ $juego->id }}"
                            wire:model="seleccionados"
                        >
                    </td>

                    <!-- Datos del juego -->
                    <td>{{ $juego->titulo }}</td>
                    <td>
                        @foreach ($juego->juegoMonedas as $moneda)
                            <a href="{{ $moneda->link }}" target="_blank">
                                {{ $moneda->moneda->region }}
                            </a>
                            <br>
                        @endforeach
                    </td>
                    <td>
                        @foreach ($juego->juegoMonedas as $moneda)
                            <p>{{ $moneda->moneda->region }}: {{ $moneda->precio_original }}</p>
                        @endforeach
                    </td>
                    <td>
                        @foreach ($juego->juegoMonedas as $moneda)
                            <p>{{ $moneda->moneda->region }}: {{ $moneda->precio_oferta }}</p>
                        @endforeach
                    </td>
                    <td>{{ $juego->plataforma }}</td>
                    <td><img src="{{ $juego->imgLowURL }}" alt="imgLow" width="128" loading="lazy"></td>

                    <!-- Botón Editar -->
                    <td>
                        <form action="{{ route('juegos.edit', $juego->id) }}" method="GET" style="display:inline;">
                            <input type="submit" value="Editar">
                        </form>
                    </td>

                    <!-- Botón Eliminar -->
                    <td>
                        <form action="{{ route('juegos.destroy', $juego->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <input type="submit" value="Eliminar"/>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Links de paginación -->
    <div style="margin-top: 20px;">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <!-- Información -->
            <div>
                Mostrando {{ $resultados->firstItem() }} - {{ $resultados->lastItem() }}
                de {{ $resultados->total() }} resultados
            </div>

            <!-- Controles de paginación -->
            <div style="display: flex; gap: 5px;">
                {{-- Botón Primera página --}}
                @if (!$resultados->onFirstPage())
                    <button wire:click="gotoPage(1)" style="padding: 5px 10px; border: 1px solid #ddd; background: white; cursor: pointer;">««</button>
                @endif

                {{-- Botón Anterior --}}
                @if ($resultados->onFirstPage())
                    <span style="padding: 5px 10px; border: 1px solid #ddd; background: #f5f5f5; color: #999;">‹</span>
                @else
                    <button wire:click="previousPage" style="padding: 5px 10px; border: 1px solid #ddd; background: white; cursor: pointer;">‹</button>
                @endif

                {{-- Números de página --}}
                @foreach(range(1, $resultados->lastPage()) as $page)
                    @if ($page == $resultados->currentPage())
                        <span style="padding: 5px 10px; border: 1px solid #007bff; background: #007bff; color: white;">{{ $page }}</span>
                    @else
                        <button wire:click="gotoPage({{ $page }})" style="padding: 5px 10px; border: 1px solid #ddd; background: white; cursor: pointer;">{{ $page }}</button>
                    @endif
                @endforeach

                {{-- Botón Siguiente --}}
                @if ($resultados->hasMorePages())
                    <button wire:click="nextPage" style="padding: 5px 10px; border: 1px solid #ddd; background: white; cursor: pointer;">›</button>
                @else
                    <span style="padding: 5px 10px; border: 1px solid #ddd; background: #f5f5f5; color: #999;">›</span>
                @endif

                {{-- Botón Última página --}}
                @if ($resultados->hasMorePages())
                    <button wire:click="gotoPage({{ $resultados->lastPage() }})" style="padding: 5px 10px; border: 1px solid #ddd; background: white; cursor: pointer;">»»</button>
                @endif
            </div>
        </div>
    </div>
</div>
