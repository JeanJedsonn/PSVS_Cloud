<?php
namespace App\Livewire;
use Livewire\Component;
use App\Services\BuscadorJuego;                 // Servicio que busca en sony
use App\Services\GuardarJuego;                  // Servicio que guarda el juego

class BuscarJuego extends Component
{
    // Campos
    public $respuesta = '';
    public $resultados = [];
    public $mensaje = [];       // Mensajes de estado para mostrar en la interfaz

    // se pasa como dependencia a la clase BuscadorJuego
    public function buscarJuego(BuscadorJuego $buscador)
    {
        if ($this->respuesta == '') return;
        $this->resultados = []; // Limpia los resultados anteriores
        $this->resultados = $buscador->buscar($this->respuesta);
    }

    // se pasa como dependencia a la clase GuardarJuego
    public function guardar(GuardarJuego $guardador, $clave){
        $guardador->guardar($this->resultados[$clave]);
        unset($this->resultados[$clave]);   // Elimina el juego de los resultados
    }

    public function render()
    {
        return view('livewire.buscarJuego');
    }
}
