@extends('layouts.bootstrap')

@section('title', 'Editar Región')

@section('content')
<div class="row justify-content-center mt-4">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-white py-3">
                <h1 class="h4 mb-0">Editar Región/Moneda</h1>
            </div>

            <div class="card-body">
                <form action="{{ route('regionMonedas.update', $regionMoneda->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label">Región</label>
                        <input type="text" name="region" class="form-control"
                            value="{{ old('region', $regionMoneda->region) }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Dirección (URL Base)</label>
                        <input type="text" name="direccion" class="form-control"
                            value="{{ old('direccion', $regionMoneda->direccion) }}" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Símbolo Moneda</label>
                            <input type="text" name="simbolo_moneda" class="form-control"
                                value="{{ old('simbolo_moneda', $regionMoneda->simbolo_moneda) }}" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tasa USD</label>
                            <input type="number" step="0.0001" name="tasa_usd" class="form-control"
                                value="{{ old('tasa_usd', $regionMoneda->tasa_usd) }}" required>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('regionMonedas.index') }}" class="btn btn-secondary">Cancelar</a>
                        <button type="submit" class="btn btn-primary">Actualizar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endSection