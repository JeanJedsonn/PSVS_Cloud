@extends('layouts.base') <!-- Extiende la plantilla base (inserta el head)-->

@section('title', 'Lista de Juegos') <!-- Cambia el titulo de la pagina -->

@section('content')
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

    <livewire:tablaJuego />--}}
@endsection