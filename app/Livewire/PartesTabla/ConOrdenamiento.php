<?php

namespace App\Livewire\PartesTabla;

trait ConOrdenamiento
{ 
    public $ordenarPor = 'titulo';
    public $ordenDireccion = 'asc';

    public function ordenar($columna)
    {
        if ($this->ordenarPor === $columna) {
            $this->ordenDireccion = $this->ordenDireccion === 'asc' ? 'desc' : 'asc';
        } else {
            $this->ordenarPor = $columna;
            $this->ordenDireccion = 'asc';
        }

        $this->resetPage();
    }

    protected function esOrdenamientoComplejo()
    {
        return $this->ordenarPor === 'tiene_oferta';
    }
}
