<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use Illuminate\Support\Facades\Http;

class ConsultarPrecio extends Component
{
    public $datos;

    public function __construct($identificador = 'dolar_bcv')
    {
        $this->datos = $this->obtenerDatos($identificador);
    }

    private function obtenerDatos($identificador)
    {
        try {
            switch ($identificador) {
                case 'dolar_bcv':
                    $response = Http::get("https://ve.dolarapi.com/v1/dolares/oficial");
                    $json = $response->json();
                    
                    return [
                        'precio' => (float) $json['promedio'],
                        'label' => '💵 Dólar Oficial (BCV)',
                        'fecha' => \Carbon\Carbon::parse($json['fechaActualizacion'])->format('d/m/Y h:i A'),
                        'color' => '#28a745' // Verde
                    ];
                
                case 'dolar_paralelo':
                    $response = Http::get("https://ve.dolarapi.com/v1/dolares/paralelo");
                    $json = $response->json();
                    
                    return [
                        'precio' => (float) $json['promedio'],
                        'label' => '💵 Dólar Paralelo',
                        'fecha' => \Carbon\Carbon::parse($json['fechaActualizacion'])->format('d/m/Y h:i A'),
                        'color' => '#28a745' // Verde
                    ];
                case 'dolar_bolivia_oficial':
                    $response = Http::get("https://bo.dolarapi.com/v1/dolares/oficial");
                    $json = $response->json();
                    
                    return [
                        'precio' => (float) $json['venta'],
                        'label' => '💵 Dólar Oficial (BOL)',
                        'fecha' => \Carbon\Carbon::parse($json['fechaActualizacion'])->format('d/m/Y h:i A'),
                        'color' => '#28a745' // Verde
                    ];
                case "dolar_bolivia_binance":
                    $response = Http::get("https://bo.dolarapi.com/v1/dolares/binance");
                    $json = $response->json();
                    
                    return [
                        'precio' => (float) $json['venta'],
                        'label' => '💵 Dólar Binance (BOL)',
                        'fecha' => \Carbon\Carbon::parse($json['fechaActualizacion'])->format('d/m/Y h:i A'),
                        'color' => '#28a745' // Verde
                    ];

                case "dolar_brasil":
                    $response = Http::get("https://br.dolarapi.com/v1/cotacoes/usd");
                    $json = $response->json();
                    
                    return [
                        'precio' => (float) $json['venda'],
                        'label' => '💵 Dólar Oficial (BRA)',
                        'fecha' => \Carbon\Carbon::parse($json['dataAtualizacao'])->format('d/m/Y h:i A'),
                        'color' => '#28a745' // Verde
                    ];
                case "dolar_chile":
                    $response = Http::get("https://cl.dolarapi.com/v1/cotizaciones/eur");
                    $json = $response->json();
                    
                    return [
                        'precio' => (float) $json['venta']*0.85,
                        'label' => '💵 Dólar Oficial (CHI)',
                        'fecha' => \Carbon\Carbon::parse($json['fechaActualizacion'])->format('d/m/Y h:i A'),
                        'color' => '#28a745' // Verde
                    ];
                // Aquí se pueden agregar más casos (ej: 'bitcoin', 'euro', etc.)
                
                default:
                    return ['error' => 'Identificador no reconocido'];
            }
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.consultar-precio');
    }
}
