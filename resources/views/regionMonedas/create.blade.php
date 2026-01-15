@extends('layouts.bootstrap')

@section('title', 'Nueva Región')

@section('content')
    <div class="row justify-content-center mt-4">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-dark text-white py-3">
                    <h1 class="h4 mb-0">Agregar Nueva Región/Moneda</h1>
                </div>

                <div class="card-body">
                    <form action="{{ route('regionMonedas.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">Región</label>
                            <input type="text" name="region" class="form-control" required placeholder="Ej: USA">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Dirección (URL Base)</label>
                            <input type="text" name="direccion" class="form-control" required
                                placeholder="Ej: en-us">
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Símbolo Moneda</label>
                                <input type="text" name="simbolo_moneda" class="form-control" required
                                    placeholder="Ej: US$">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tasa USD</label>
                                <input type="number" step="0.0001" name="tasa_usd" class="form-control" required
                                    placeholder="Ej: 1.00">
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="{{ route('regionMonedas.index') }}" class="btn btn-secondary">Cancelar</a>
                            <button type="submit" class="btn btn-primary">Guardar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection