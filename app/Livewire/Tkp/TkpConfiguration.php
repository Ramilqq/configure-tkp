<?php

namespace App\Livewire\Tkp;

use App\Models\Tkp\Tkp;
use Livewire\Component;

class TkpConfiguration extends Component
{
    public int $tkp_version = 0;
    public int $id = 0;

    public function mount($id = 0, $tkp_version = 0)
    {
        $this->tkp_version = $tkp_version;
        $this->id = $id;

        // Проверка авторизации
        if ($this->id && $this->tkp_version) {
            $tkp = Tkp::findOrFail($this->id);
            $this->authorize('view', $tkp);
        }
    }

    public function render()
    {
        return view('livewire.tkp.tkp-configuration');
    }
}
