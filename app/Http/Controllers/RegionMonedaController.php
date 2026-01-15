<?php

namespace App\Http\Controllers;

use App\Models\Moneda;
use App\Http\Requests\StoreRegionMonedaRequest;
use App\Http\Requests\UpdateRegionMonedaRequest;

class RegionMonedaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $regionMonedas = Moneda::all();
        return view('regionMonedas.index', compact('regionMonedas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('regionMonedas.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRegionMonedaRequest $request)
    {
        Moneda::create($request->validated());
        return redirect()->route('regionMonedas.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Moneda $regionMoneda)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Moneda $regionMoneda)
    {
        return view('regionMonedas.edit', compact('regionMoneda'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRegionMonedaRequest $request, Moneda $regionMoneda)
    {
        $regionMoneda->update($request->validated());
        return redirect()->route('regionMonedas.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Moneda $regionMoneda)
    {
        $regionMoneda->juegoMonedas()->delete();
        $regionMoneda->delete();
        return redirect()->route('regionMonedas.index');
    }
}
