<?php

namespace App\Livewire\Forms\Configuration;

use App\Models\Configuration\Node;
use App\Services\StringTranslit;
use Livewire\Form;

class NodeForm extends Form
{
    const TOP_POSITION      = 0.0;
    const BOTTOM_POSITION   = 1.0;

    public int $id = 0;
    public int $node_group_id = 0;
    public int $template_id = 0;
    public string $type = '';
    public string $title = '';
    public string $name = '';
    public string $extra = '';
    public $image_upload;
    public $image;
    public string $endpoints = '';
    public array $endpoints_arr = [];
    public $label_fields = [];
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
            'template_id' => 'required|numeric|exists:templates,id',
            'type' => 'required|min:2|max:100|unique:nodes,type,'.$this->id,
            'title' => 'required|min:2|max:100',
            'name' => 'min:1|max:100',
            'extra' => 'nullable|max:255',
            'image_upload' => 'nullable|image|mimes:jpg,png,jpeg,svg|max:10480000|dimensions:max_height=500',
            'image' => 'required|max:10480000',
            'endpoints_arr' => 'required',
            'endpoints' => 'required',
            'label_fields' => 'nullable|array',
            // правило для key обязательно: validate() отбрасывает вложенные ключи без правил,
            // и без него key вырезался бы из сохраняемых данных
            'label_fields.*.key' => 'nullable|string|max:100',
            'label_fields.*.title' => 'required|string|max:100',
            'label_fields.*.format' => 'nullable|string|max:100',
            'label_fields.*.x' => 'required|numeric|min:-50|max:150',
            'label_fields.*.y' => 'required|numeric|min:-50|max:150',
            'label_fields.*.options' => 'nullable|array',
            'label_fields.*.options.*.title' => 'required|string|max:100',
            'label_fields.*.options.*.format' => 'required|string|max:100',
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

    // точная подстройка X точки подключения кнопками по бокам ползунка
    public function nudgeAnchorX($key, $delta)
    {
        if (!isset($this->endpoints_arr[$key])) return;

        $x = (float)($this->endpoints_arr[$key]['anchor']['anchor_x'] ?? 0) + (float)$delta;
        $this->endpoints_arr[$key]['anchor']['anchor_x'] = round(max(0, min(1, $x)), 2);
    }

    // точная подстройка X/Y подписи кнопками по бокам ползунка
    public function nudgeLabelField($key, $axis, $delta)
    {
        if (!isset($this->label_fields[$key]) || !in_array($axis, ['x', 'y'], true)) return;

        $v = (float)($this->label_fields[$key][$axis] ?? 0) + (float)$delta;
        $this->label_fields[$key][$axis] = round(max(-50, min(150, $v)));
    }

    public function addLabelField()
    {
        $this->label_fields[] = [
            'key'     => '',
            'title'   => '',
            'format'  => '{value}',
            'x'       => 50,
            'y'       => -10,
            'options' => [],
        ];
    }

    public function dllLabelField($key)
    {
        unset($this->label_fields[$key]);

        $this->label_fields = array_values($this->label_fields);
    }

    // вариант типа для подписи: свой формат вывода на каждый тип (контактор → КМ{value}, выключатель → Q{value})
    public function addLabelFieldOption($fieldKey)
    {
        $this->label_fields[$fieldKey]['options'][] = [
            'title'  => '',
            'format' => '{value}',
        ];
    }

    public function dllLabelFieldOption($fieldKey, $optionKey)
    {
        unset($this->label_fields[$fieldKey]['options'][$optionKey]);

        $this->label_fields[$fieldKey]['options'] = array_values($this->label_fields[$fieldKey]['options']);
    }
    
    public function saveForm($id = null)
    {
        // транслит рус в англ
        $this->type = StringTranslit::transliterate($this->title);

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

        // нормализация подписей на схеме: числа, ключ из транслита заголовка
        $usedKeys = [];
        foreach ($this->label_fields as $lf_key => $lf) {
            $this->label_fields[$lf_key]['x'] = floatval($lf['x'] ?? 50);
            $this->label_fields[$lf_key]['y'] = floatval($lf['y'] ?? 0);
            $this->label_fields[$lf_key]['options'] = array_values($lf['options'] ?? []);

            $key = trim((string)($lf['key'] ?? ''));
            if ($key === '') {
                $key = StringTranslit::transliterate((string)($lf['title'] ?? ''));
            }
            // ключи должны быть уникальны в рамках узла — значения хранятся по ним
            $base = $key;
            $i = 2;
            while (in_array($key, $usedKeys, true)) {
                $key = $base . '_' . $i++;
            }
            $usedKeys[] = $key;
            $this->label_fields[$lf_key]['key'] = $key;
        }
        $this->label_fields = array_values($this->label_fields);

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
        //$this->reset();

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
        $this->label_fields = $template->label_fields ?: [];
    }
}
