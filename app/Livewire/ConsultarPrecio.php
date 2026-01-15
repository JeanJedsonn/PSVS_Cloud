<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Lazy;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

#[Lazy]
class ConsultarPrecio extends Component
{
    public $identificador;
    public $datos;

    public function mount($identificador = 'dolar_bcv')
    {
        $this->identificador = $identificador;
        $this->datos = $this->obtenerDatos($identificador);
    }

    public function placeholder()
    {
        return <<<'HTML'
        <div style="border: 1px solid #ccc; border-radius: 8px; padding: 10px; width: fit-content; margin-bottom: 20px; display: inline-block;">
            <h3 style="margin: 0; color: #555; font-size: 1em;">Cargando...</h3>
            <p style="font-size: 1.2em; font-weight: bold; margin: 5px 0 0 0; color: #999;">
                ...
            </p>
        </div>
        HTML;
    }

    private function obtenerDatos($identificador)
    {
        try {
            return Cache::remember("precio_{$identificador}", 60, function () use ($identificador) {
                switch ($identificador) {
                    case 'dolar_bcv':
                        $response = Http::get("https://ve.dolarapi.com/v1/dolares/oficial");
                        $json = $response->json();
                        
                        return [
                            'precio' => $json['promedio'].'Bs',
                            'label' => '💵 Dólar (BCV)',
                            'fecha' => \Carbon\Carbon::parse($json['fechaActualizacion'])->format('d/m/Y h:i A'),
                            'color' => '#28a745' // Verde
                        ];
                    
                    case 'dolar_paralelo':
                        $response = Http::get("https://ve.dolarapi.com/v1/dolares/paralelo");
                        $json = $response->json();
                        
                        return [
                            'precio' => $json['promedio'].'Bs',
                            'label' => '💵 Dólar Paralelo',
                            'fecha' => \Carbon\Carbon::parse($json['fechaActualizacion'])->format('d/m/Y h:i A'),
                            'color' => '#28a745' // Verde
                        ];

                    case 'dolar_dif':
                        $responseParalelo = Http::get("https://ve.dolarapi.com/v1/dolares/paralelo");
                        $responseOficial = Http::get("https://ve.dolarapi.com/v1/dolares/oficial");
                        $jsonParalelo = $responseParalelo->json();
                        $jsonOficial = $responseOficial->json();
                        
                        $difDolar = ($jsonParalelo['promedio'] - $jsonOficial['promedio']) / $jsonOficial['promedio'];
                        $difEuro = ($jsonParalelo['promedio'] - $jsonOficial['promedio']*1.13 ) / ($jsonOficial['promedio']*1.13);
                        return [
                            'precio' => '💲'.number_format($difDolar*100, 2, ".", ",")."%"."\n"."💶".number_format($difEuro*100, 2, ".", ",")."%",    
                            'label' => '📏 Diferencia',
                            'fecha' => \Carbon\Carbon::parse($jsonParalelo['fechaActualizacion'])->format('d/m/Y h:i A'),
                            'color' => '#28a745' // Verde
                        ];

                    case 'dolar_bolivia_oficial':
                        $response = Http::get("https://bo.dolarapi.com/v1/dolares/oficial");
                        $json = $response->json();
                        
                        return [
                            'precio' => (float) $json['venta'] . "BOB",
                            'label' => '💵 Dólar Oficial (BOL)',
                            'fecha' => \Carbon\Carbon::parse($json['fechaActualizacion'])->format('d/m/Y h:i A'),
                            'color' => '#28a745' // Verde
                        ];

                    case "dolar_bolivia_binance":
                        $response = Http::get("https://bo.dolarapi.com/v1/dolares/binance");
                        $json = $response->json();
                        
                        return [
                            'precio' => (float) $json['venta'] . "BOB",
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
                    
                    default:
                        throw new \Exception('Identificador no reconocido');
                }
            });
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function render()
    {
        return view('livewire.consultar-precio');
    }
}


/*
<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Lazy;
use Illuminate\Support\Facades\Http;

#[Lazy]
class ConsultarPrecio extends Component
{
    public $identificador;
    public $datos;

    public function mount($identificador = 'dolar_bcv')
    {
        $this->identificador = $identificador;
        $this->datos = $this->obtenerDatos($identificador);
    }

    public function placeholder()
    {
        return <<<'HTML'
        <div style="border: 1px solid #ccc; border-radius: 8px; padding: 10px; width: fit-content; margin-bottom: 20px; display: inline-block;">
            <h3 style="margin: 0; color: #555; font-size: 1em;">Cargando...</h3>
            <p style="font-size: 1.2em; font-weight: bold; margin: 5px 0 0 0; color: #999;">
                ...
            </p>
        </div>
        HTML;
    }

    private function obtenerDatos($identificador)
    {
        try {
            switch ($identificador) {
                case 'dolar_bcv':
                    $response = Http::get("https://ve.dolarapi.com/v1/dolares/oficial");
                    $json = $response->json();
                    
                    return [
                        'precio' => $json['promedio'].'Bs',
                        'label' => '💵 Dólar (BCV)',
                        'fecha' => \Carbon\Carbon::parse($json['fechaActualizacion'])->format('d/m/Y h:i A'),
                        'color' => '#28a745' // Verde
                    ];
                
                case 'dolar_paralelo':
                    $response = Http::get("https://ve.dolarapi.com/v1/dolares/paralelo");
                    $json = $response->json();
                    
                    return [
                        'precio' => $json['promedio'].'Bs',
                        'label' => '💵 Dólar Paralelo',
                        'fecha' => \Carbon\Carbon::parse($json['fechaActualizacion'])->format('d/m/Y h:i A'),
                        'color' => '#28a745' // Verde
                    ];
                case 'dolar_dif':
                    $responseParalelo = Http::get("https://ve.dolarapi.com/v1/dolares/paralelo");
                    $responseOficial = Http::get("https://ve.dolarapi.com/v1/dolares/oficial");
                    $jsonParalelo = $responseParalelo->json();
                    $jsonOficial = $responseOficial->json();
                    
                    $difDolar = ($jsonParalelo['promedio'] - $jsonOficial['promedio']) / $jsonOficial['promedio'];
                    $difEuro = ($jsonParalelo['promedio'] - $jsonOficial['promedio']*1.13 ) / ($jsonOficial['promedio']*1.13);
                    return [
                        'precio' => '💲'.number_format($difDolar*100, 2, ".", ",")."%"."\n"."💶".number_format($difEuro*100, 2, ".", ",")."%",    
                        'label' => '📏 Diferencia',
                        'fecha' => \Carbon\Carbon::parse($jsonParalelo['fechaActualizacion'])->format('d/m/Y h:i A'),
                        'color' => '#28a745' // Verde
                    ];

                case 'dolar_bolivia_oficial':
                    $response = Http::get("https://bo.dolarapi.com/v1/dolares/oficial");
                    $json = $response->json();
                    
                    return [
                        'precio' => (float) $json['venta'] . "BOB",
                        'label' => '💵 Dólar Oficial (BOL)',
                        'fecha' => \Carbon\Carbon::parse($json['fechaActualizacion'])->format('d/m/Y h:i A'),
                        'color' => '#28a745' // Verde
                    ];
                case "dolar_bolivia_binance":
                    $response = Http::get("https://bo.dolarapi.com/v1/dolares/binance");
                    $json = $response->json();
                    
                    return [
                        'precio' => (float) $json['venta'] . "BOB",
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
                
                default:
                    return ['error' => 'Identificador no reconocido'];
            }
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function render()
    {
        return view('livewire.consultar-precio');
    }
}

 */