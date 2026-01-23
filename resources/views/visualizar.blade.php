@extends('layouts.base')

@section('title', 'Visualizar Tienda')

@section('content')
    <div class="container-fluid p-0" style="height: calc(100vh - 100px);">
        <div class="card h-100 shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center py-2">
                <div class="d-flex align-items-center gap-3">
                    <h5 class="mb-0 fw-bold d-flex align-items-center gap-2">
                        <span class="text-primary">👁️</span> Visualizador de Tienda
                    </h5>
                    
                    <div class="d-flex align-items-center gap-2 border-start ps-3">
                        <label for="regionSelect" class="form-label mb-0 small text-muted">Región:</label>
                        <select id="regionSelect" class="form-select form-select-sm" style="width: auto;">
                            @foreach($monedas as $moneda)
                                <option value="{{ $moneda->direccion }}" {{ $moneda->direccion === 'en-us' ? 'selected' : '' }}>
                                    {{ $moneda->region }} ({{ $moneda->direccion }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="text-muted small">
                    <i class="bi bi-info-circle"></i> Navegando en: <strong id="urlDisplay">store.playstation.com</strong>
                </div>
            </div>
            <div class="card-body p-0">
                <iframe id="storeFrame"
                        src="https://store.playstation.com/en-us/pages/latest" 
                        frameborder="0" 
                        class="w-100 h-100" 
                        allow="fullscreen"
                        referrerpolicy="no-referrer"
                        sandbox="allow-same-origin allow-scripts allow-popups allow-forms">
                </iframe>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const select = document.getElementById('regionSelect');
            const iframe = document.getElementById('storeFrame');
            const urlDisplay = document.getElementById('urlDisplay');

            function updateRegion() {
                const region = select.value;
                const newUrl = `https://store.playstation.com/${region}/pages/latest`;
                iframe.src = newUrl;
                urlDisplay.textContent = newUrl.replace('https://', '');
            }

            select.addEventListener('change', updateRegion);
            
            // Initialize with correct default if needed
            // updateRegion(); 
        });
    </script>
@endsection
