<?php

namespace App\Livewire\PartesTabla;

trait ConPaginacion
{
    public $porPagina = 10;

    public function updatedPorPagina()
    {
        $this->resetPage();
    }

    protected function obtenerPorPagina()
    {
        return $this->porPagina;
    }
}
