<?php

namespace App\Livewire\Tkp;

use App\Models\Tkp\Tkp;
use Livewire\Component;

class TkpList extends Component
{

    public function delete($id)
    {
        Tkp::find($id)->delete();
    }

    public function render()
    {
        $tkp = Tkp::all();
        return view('livewire.tkp.tkp-list', ['tkp' => $tkp]);
    }
}
