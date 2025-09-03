<?php

namespace App\Livewire\Tkp;

use Livewire\Component;

class TkpConfiguration extends Component
{
    public int $tkp_version = 0;
    public int $id = 0;

    public function mount($id = 0, $tkp_version = 0)
    {
        $this->tkp_version = $tkp_version;
        $this->id = $id;
    }

    public function render()
    {
        return view('livewire.tkp.tkp-configuration');
    }
}
