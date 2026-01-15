<?php

namespace App\Livewire\PartesTabla;

trait ConSeleccion
{
    public $seleccionados = [];

    public function toggleTodos($idsEnPagina)
    {
        if (count(array_intersect($this->seleccionados, $idsEnPagina)) === count($idsEnPagina)) {
            $this->seleccionados = array_diff($this->seleccionados, $idsEnPagina);
        } else {
            $this->seleccionados = array_unique(array_merge($this->seleccionados, $idsEnPagina));
        }
    }

    public function limpiarSeleccion()
    {
        $this->seleccionados = [];
    }

    public function tieneSeleccionados()
    {
        return count($this->seleccionados) > 0;
    }

    public function contarSeleccionados()
    {
        return count($this->seleccionados);
    }
}
