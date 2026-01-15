<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\JuegoController;
use App\Http\Controllers\RegionMonedaController;



// rutas que requieren autenticacion
Route::middleware(['auth', 'verified'])->group(function () {    //middleware que se ejecuta antes de mostrar la vista
    Route::get("/", [JuegoController::class, "index"])
        ->name("index");

    Route::resource('juegos', JuegoController::class)
        ->except(["index", "show"]);

    Route::get('buscar', [JuegoController::class, 'buscar'])
        ->name('juegos.buscar');

    Route::resource('regionMonedas', RegionMonedaController::class)
        ->except(["show"]);
});
// rutas generadas para autenticacion
require __DIR__ . '/auth.php';
