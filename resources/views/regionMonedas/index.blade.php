@extends('layouts.bootstrap')           <!-- Extiende el layout base (bootstrap.blade.php)-->

@section('title', 'Lista de Monedas')    <!-- Define el título de la página (sale en la pestaña del navegador)-->

@section('content') <!-- Contenido de la página-->

    <!-- Contenedor de la tabla -->
    <div class="card shadow-sm mt-4">

        <!-- Encabezado de la tabla -->
        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
            <h1 class="h4 mb-0">Regiones Configuradas</h1>
            <a href="{{ route('regionMonedas.create') }}" class="btn btn-primary btn-sm">
                + Nueva Región
            </a>
        </div>
        
        <!-- Cuerpo de la tabla -->
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover mb-0">

                    <!-- Columnas -->
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Región</th>
                            <th>Dirección</th>
                            <th>Símbolo</th>
                            <th>Tasa USD</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>

                    <!-- Filas -->
                    <tbody>
                        @forelse ($regionMonedas as $regionMoneda)
                            <tr>
                                <td>{{ $regionMoneda->id }}</td>
                                <!-- fw-medium es negrita -->
                                <td class="fw-medium">{{ $regionMoneda->region }}</td>
                                <td>{{ $regionMoneda->direccion }}</td>
                                <td>{{ $regionMoneda->simbolo_moneda }}</td>
                                <td>{{ $regionMoneda->tasa_usd }}</td>
                                <td>
                                    <div class="text-center">
                                        <a href="{{ route('regionMonedas.edit', $regionMoneda->id) }}" 
                                           class="btn btn-primary">
                                            Editar
                                        </a>
                                        
                                        <form action="{{ route('regionMonedas.destroy', $regionMoneda->id) }}" 
                                              method="POST" 
                                              class="d-inline"
                                              onsubmit="return confirm('¿Seguro que deseas eliminar esta región/moneda? (Borrará todos los precios asociados)')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger">
                                                Eliminar
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">
                                    No hay regiones registradas
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection