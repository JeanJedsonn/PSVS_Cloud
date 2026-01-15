<?php

namespace App\Livewire\PartesTabla;

trait ConBusqueda
{
    public $busqueda = '';
    public $busquedaInput = '';

    public function buscar()
    {
        $this->busqueda = trim($this->busquedaInput);
        $this->resetPage();
    }

    public function limpiarBusqueda()
    {
        $this->busqueda = '';
        $this->busquedaInput = '';
        $this->resetPage();
    }

    protected function aplicarBusqueda($query)
    {
        return $query->when($this->busqueda, function($q) {
            $q->where('titulo', 'ILIKE', '%' . $this->busqueda . '%');
        });
    }
}
