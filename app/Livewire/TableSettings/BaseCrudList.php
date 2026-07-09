<?php

namespace App\Livewire\TableSettings;

use Illuminate\Database\Eloquent\Model;
use Livewire\Component;

/**
 * Базовый компонент списка справочника: рендер, удаление,
 * обновление по событию от Edit-компонента.
 */
abstract class BaseCrudList extends Component
{
    /** @return class-string<Model> */
    abstract protected function modelClass(): string;

    /** Blade-шаблон списка */
    abstract protected function viewName(): string;

    /** Имя переменной коллекции в blade-шаблоне */
    abstract protected function viewVariable(): string;

    /** Событие обновления списка, которое диспатчит Edit-компонент */
    abstract protected function updateEvent(): string;

    protected function getListeners(): array
    {
        return [$this->updateEvent() => '$refresh'];
    }

    public function delete($id)
    {
        $model = $this->modelClass()::findOrFail($id);
        $this->authorize('delete', $model);

        $model->delete();
    }

    public function render()
    {
        $modelClass = $this->modelClass();
        $this->authorize('view', new $modelClass);

        $items = $modelClass::query()->orderByDesc('id')->get();

        return view($this->viewName(), [$this->viewVariable() => $items]);
    }
}
