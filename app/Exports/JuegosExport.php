<?php

namespace App\Exports;

use App\Models\Juego;
//use App\Models\JuegoMoneda;
use App\Models\Moneda;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class JuegosExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $ids;
    protected $retornarTodos;

    public function __construct(array $ids)
    {
        $this->ids = $ids;
        Log::info('Exportando juegos '. json_encode($ids));
        $this->retornarTodos = count($ids) == 0 ? true : false;
    }

    // Retorna todos los juegos ()
    public function collection()
    {
        Log::info('Exportando juegos');
        $retornar = $this->retornarTodos ? Juego::all() : Juego::whereIn('id', $this->ids)->get();
        //dd($retornar);
        return $retornar;
    }

    // Retorna los encabezados
    public function headings(): array
    {
        $regiones = Moneda::All()->pluck('region')->toArray();
        $retornar = [
            'ID',
            'Título',
            'PS4',
            'PS5',
        ];

        foreach ($regiones as $region) {

            array_push($retornar ,
            'Precio Original: '.$region,
            "Precio Oferta: ".$region
            );
        }

        array_push($retornar,
            'Imagen',
        );

        return $retornar;
    }

    // Retorna los datos de cada juego (no se como funciona esto)
    public function map($juegos): array
    {
        $rows = [];
        $monedasDesordenadas = [];
        $monedasOrdenadas = Moneda::All()->pluck('id')->toArray();      // copia en orden las monedas de la BD
        $showPS4 = 'hide';
        $showPS5 = 'hide';
        $plataforma = explode(' & ', $juegos->plataforma);



        if (in_array('PS4', $plataforma)) {
            $showPS4 = 'show';
        }
        if (in_array('PS5', $plataforma)) {
            $showPS5 = 'show';
        }

        array_push($rows,
            $juegos->id,
            $juegos->titulo,
            $showPS4,
            $showPS5
        );

        // copia las monedas en un array desordenado
        foreach ($juegos->juegoMonedas as $juegoMoneda) {
            $monedasDesordenadas[$juegoMoneda->moneda_id] = $juegoMoneda;
        }

        // recorre las monedas desordenadas en el orden de la BD
        foreach ($monedasOrdenadas as $moneda) {
            if (!isset($monedasDesordenadas[$moneda])) {
                array_push($rows,
                    '',
                    '',
                );
                continue;
            }
            else{
                $precioOriginal = $monedasDesordenadas[$moneda]->precio_original;
                $precioOferta = $monedasDesordenadas[$moneda]->precio_oferta;
                array_push($rows,
                    $precioOriginal,
                    $precioOferta,
                );
            }

        }

        array_push($rows,
            $juegos->imgURL,
        );


        return $rows;
    }

    // Estilos de las celdas del encabezado
    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }
}
//JuegosSeleccionadosExport.php
