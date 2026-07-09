<?php

namespace App\Livewire\Tkp;

use App\Models\Tkp\Tkp;
use Livewire\Component;

class TkpList extends Component
{
    public function delete($id)
    {
        $tkp = Tkp::findOrFail($id);

        // Проверка авторизации
        $this->authorize('delete', $tkp);

        $tkp->delete();
    }

    public function render()
    {
        $tkp = Tkp::query()->orderByDesc('id')->get();

        return view('livewire.tkp.tkp-list', ['tkp' => $tkp]);
    }
}
