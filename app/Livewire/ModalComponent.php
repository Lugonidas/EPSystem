<?php

namespace App\Http\Livewire;

use Livewire\Component;

class ModalComponent extends Component
{
    public $route;
    public $formTitle;
    public $btnCancelar;

    public function mount($route, $formTitle, $btnCancelar)
    {
        $this->route = $route;
        $this->formTitle = $formTitle;
        $this->btnCancelar = $btnCancelar;
    }

    public function render()
    {
        return view('livewire.modal-component');
    }
}
