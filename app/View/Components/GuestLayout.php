<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class GuestLayout extends Component
{
    /**
     * Get the view / contents that represents the component.
     */
    public function render(): View
    {
        //guest es un layout que se muestra cuando el usuario no esta autenticado
        return view('layouts.guest');
    }
}
