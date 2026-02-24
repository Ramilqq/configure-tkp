<?php

namespace App\Livewire\Tkp;

use App\Models\Tkp\Tkp;
use Livewire\Component;
use Illuminate\Support\Facades\Cache;

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
        $tkp = Cache::remember('tkp_list', now()->addHours(6), function () {
            return Tkp::all()->sortDesc();
        });

        return view('livewire.tkp.tkp-list', ['tkp' => $tkp]);
    }
}
