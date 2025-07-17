<?php

namespace App\Livewire\Forms\Configuration;

use App\Models\Configuration\Node;
use App\Services\StringTranslit;
use Livewire\Form;

class NodeForm extends Form
{
    const TOP_POSITION      = -0.1;
    const BOTTOM_POSITION   = 1.1;

    public int $id = 0;
    public int $node_group_id = 0;
    public string $type = '';
    public string $name = '';
    public $image_upload;
    public $image;
    public string $endpoints = '';
    public array $endpoints_arr = [];
    public array $anchor = [
        'anchor' => [
            'anchor_x'      => 0,   // положение по вертикали 0 - 1
            'anchor_y'      => self::TOP_POSITION,  // положение по горизантали 0 - 1
            'anchor_dx'     => 0,   // Горизонтальное направление стрелки/линии, выходящей из точки: 1 — направо, -1 — налево, 0 — без смещения
            'anchor_dy'     => 1,   // Вертикальное направление линии: 1 — вниз, -1 — вверх, 0 — без смещения
        ],
        'isSource'      => true,   // вход
        'isTarget'      => true,   // выход
    ];
    public array $anchor_y = [
        'top'       => self::TOP_POSITION,
        'bottom'    => self::BOTTOM_POSITION,
    ];

    protected function rules()
    {
        return [
            'node_group_id' => 'required|exists:node_groups,id',
            'type' => 'required|min:3|max:20|unique:nodes,name,'.$this->id,
            'name' => 'required|min:3|max:20|unique:nodes,name,'.$this->id,
            'image_upload' => 'nullable|image|mimes:jpg,png,jpeg,svg|max:10480000|dimensions:max_height=120',
            'image' => 'required|max:10480000',
            'endpoints_arr' => 'required',
            'endpoints' => 'required',
        ];
    }

    public function init()
    {
        $this->anchor['anchor']['anchor_y'] = $this->anchor_y['top'];
        $this->endpoints_arr[] = $this->anchor;
    }

    public function addAnchor()
    {
        $this->endpoints_arr[] = $this->anchor;
    }

    public function dllAnchor($key)
    {
        unset($this->endpoints_arr[$key]);

        $this->endpoints_arr = array_values($this->endpoints_arr); 
    }
    
    public function saveForm($id = null)
    {
        // транслит рус в англ
        $this->type = StringTranslit::transliterate($this->name);

        // преоброзовывание строки в число
        foreach ($this->endpoints_arr as $endp_key => $endp_value)
        {
            $this->endpoints_arr[$endp_key]['anchor']['anchor_x'] = floatval($this->endpoints_arr[$endp_key]['anchor']['anchor_x']);
            $this->endpoints_arr[$endp_key]['anchor']['anchor_y'] = floatval($this->endpoints_arr[$endp_key]['anchor']['anchor_y']);

            // направление линни с точки подключения в зависимости от положения, верх или низ
            if ($this->endpoints_arr[$endp_key]['anchor']['anchor_y'] >= 1)
            {
                $this->endpoints_arr[$endp_key]['anchor']['anchor_dx'] = 0;
                $this->endpoints_arr[$endp_key]['anchor']['anchor_dy'] = 1;
            }
            if ($this->endpoints_arr[$endp_key]['anchor']['anchor_y'] <= 0)
            {
                $this->endpoints_arr[$endp_key]['anchor']['anchor_dx'] = 0;
                $this->endpoints_arr[$endp_key]['anchor']['anchor_dy'] = -1;
            }
        }

        // сохранение в json
        $this->endpoints = json_encode($this->endpoints_arr);

        // если есть картинка то сохранение в svg
        if ($this->image_upload)
        {
            $this->image = 'data:image/jpg;base64,' . base64_encode(file_get_contents($this->image_upload->getRealPath()));
        }

        // валидация данных
        $valideate = $this->validate();
        
        // посик для изменения если найдена модель, если нет создать новую
        $template = Node::find($this->id);
        if($template)
        {
            $template->update($valideate);
            $template->save();
        }
        else
        {
            $template = Node::create($valideate);
        }

        // сохраняние node_group_id в буфер 
        $bufer_node_group_id = $this->node_group_id;

        // сброс всех полей
        $this->reset();

        // сохранение node_group_id из буфера для повторной создании модели
        $this->node_group_id = $bufer_node_group_id;

        // возвращение модели
        return $template;
    }

    public function editForm($id)
    {
        $template = Node::find($id);
        $this->fill($template);
        $this->endpoints_arr = json_decode($this->endpoints, 1);

    }
}
