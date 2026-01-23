<?php

namespace App\Http\Controllers;

use App\Models\Juego;                           //ruta con el modelo y sus protecciones
use App\Http\Requests\StoreJuegoRequest;
use App\Http\Requests\UpdateJuegoRequest;
use App\Services\Buscador;
use Illuminate\Http\Request;

class JuegoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //$juegos = Juego::paginate(5);

        //retorna la vista con la lista de juegos (se indica la variable como una cadeana)
        //return view('index', compact('juegos'));
        return view('index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //retorna la vista para crear un nuevo juego, deben estar vacios los campos
        return view('juegos.create');
    }

    public function buscar()
    {
        //retorna la vista para buscar un juego, deben estar vacios los campos
        return view('juegos.buscar');
    }
    /*
    public function resultados(Request $request)
    {
        $titulo = $request->input('titulo');
        $buscador = new Buscador();
        $regiones = $buscador->buscar($titulo);

        // Retorna la vista con los resultados de la búsqueda
        return view('juegos.resultados', compact('regiones'));
    }*/

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreJuegoRequest $request)   //se usa StoreJuegoRequest para validar y almacenar el nuevo juego
    {
        $juego = Juego::create($request->validated());
        $juego->save();
        return redirect()->route('index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Juego $juego)
    {
        $nuevoID = $juego->id;
        return view('juegos.edit', compact('juego'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateJuegoRequest $request, Juego $juego)
    {
        $juego->update($request->validated());
        return redirect()->route('index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Juego $juego)
    {
        $juego->juegoMonedas()->delete();
        $juego->delete();
        return redirect()->route('index');
    }

    public function visualizar()
    {
        $monedas = \App\Models\Moneda::all();
        return view('visualizar', compact('monedas'));
    }
}
