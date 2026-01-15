{{-- @extends('layouts.base') <!-- Extiende la plantilla base (inserta el head)-->

@section('title', 'Lista de Juegos') <!-- Cambia el titulo de la pagina -->

@section('content') --}}

<!-- nin -->

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset='utf-8'>
    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <title>@yield('title', 'PS Virtual Store')</title>
</head>

<body class="bg-light">

    <!-- Barra de navegación -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4 shadow-sm">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('index') }}">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" width="40" height="40" class="d-inline-block align-text-top">
                <span class="fw-bold">PS Virtual Store</span>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="mainNavbar">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0 gap-2">
                    <li class="nav-item">
                        <a href="{{ route('index') }}" class="nav-link {{ request()->routeIs('juegos.index') ? 'active' : '' }}">
                            🎮 Juegos
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('juegos.buscar') }}" class="nav-link {{ request()->routeIs('juegos.buscar') ? 'active' : '' }}">
                            🔍 Buscar Juego
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('regionMonedas.index') }}" class="nav-link {{ request()->routeIs('regionMonedas.index') ? 'active' : '' }}">
                            🌐 Regiones y Monedas
                        </a>
                    </li>
                    <li class="nav-item">
                        <livewire:logout-button />
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <!-- Tabla de precios -->

    {{--  
    <table>
        <tr>
            <td>
                <livewire:consultar-precio identificador='dolar_bcv' />
            </td>
            <td>
                <livewire:consultar-precio identificador="dolar_paralelo" />
            </td>
            <td>
                <livewire:consultar-precio identificador="dolar_dif" />
            </td>
            <td>
                <livewire:consultar-precio identificador="dolar_bolivia_oficial" />
            </td>
            <td>
                <livewire:consultar-precio identificador="dolar_bolivia_binance" />
            </td>
            <td>
                <livewire:consultar-precio identificador="dolar_brasil" />
            </td>
            <td>
                <livewire:consultar-precio identificador="dolar_chile" />
            </td>
        </tr>
    </table>
    
    <livewire:contador-job />

    <livewire:tablaJuego />
@endsection
    --}}

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>