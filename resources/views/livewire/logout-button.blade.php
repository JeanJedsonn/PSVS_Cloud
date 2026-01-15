<?php
// TODO: quitar el boton de logout cuando se termine el proyecto
use App\Livewire\Actions\Logout;
use Livewire\Volt\Component;

new class extends Component {
    /**
     * Log the current user out of the application.
     */
    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect('/', navigate: true);
    }
}; ?>



<a wire:click.prevent="logout" href="#"
    style="color: white; text-decoration: none; padding: 8px 15px; border-radius: 3px; display: block; {{ request()->routeIs('juegos.create') ? 'background: #34495e;' : '' }}">
    ❌ Log out
</a>