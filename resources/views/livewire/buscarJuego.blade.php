<div>
    <!-- Formulario para buscar un juego -->
    {{-- wire:submit se le asigna la funcion a llmar del componente --}}
    <div class="col-12 col-md-auto mb-4">
        <form wire:submit="buscarJuego" class="d-flex gap-2">
            <div class="input-group">
                <!-- Input de búsqueda, wire:model se le asigna la variable del componente -->
                <input class="form-control" type="text" placeholder="Buscar..." wire:model="respuesta">
                <!-- Botón para buscar el juego, no se le pasa ninguna accion -->
                <button type="submit" class="input-group-text btn btn-primary">🔍</button>
            </div>
        </form>
    </div>

    <!-- Grid de Juegos -->
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 20px;">
        @if(!isset($resultados["error"]))
            @forelse ($resultados as $clave => $juego)
                <x-game-card :cover="$juego['imagenURL']" :title="$juego['titulo']" :platform="$juego['consola']">

                    {{-- insertar precios --}}
                    <x-slot:slot>
                        @foreach ($juego['precioOriginal'] as $region => $precio)
                            <div
                                style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px; border-bottom: 1px solid #eee; padding-bottom: 4px;">
                                <a href="{{ $juego['link'][$region] }}" target="_blank"
                                    style="text-decoration: none; color: #007bff; font-weight: 500;">
                                    {{ $region }}
                                </a>
                                <div style="text-align: right;">
                                    <div style="color: #666; font-size: 0.9em;">{{ $precio }}</div>
                                    @if($juego['precioActual'][$region] !== '-' && $juego['precioActual'][$region] !== 'N/A')
                                        <div style="color: #28a745; font-weight: bold;">{{ $juego['precioActual'][$region] }}</div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </x-slot:slot>

                    {{-- insertar botones de acciones --}}
                    <x-slot:actions>
                        <button type="submit" class="btn btn-sm btn-primary" wire:click="guardar('{{ $clave }}')" wire:loading.attr="disabled">Guardar</button>
                    </x-slot:actions>

                </x-game-card>
            @empty
                <div
                    style="grid-column: 1 / -1; text-align: center; padding: 40px; color: #999; border: 2px dashed #ddd; border-radius: 8px;">
                    No hay juegos disponibles
                </div>
            @endforelse
        @else
            <div
                style="grid-column: 1 / -1; text-align: center; padding: 40px; color: #999; border: 2px dashed #ddd; border-radius: 8px;">
                {{ $resultados["error"] }}
            </div>
        @endif
    </div>


</div>