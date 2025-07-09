<?php

namespace App\Livewire\Forms\TableSettings;

use App\Models\TableSettings\TemplateOption;
use Livewire\Attributes\Validate;
use Livewire\Form;

class TemplateOptionForm extends Form
{
    public int $id = 0;
    public int $template_id = 0;
    public string $name = '';
    public string $key = '';

    protected function rules()
    {
        return [
            'template_id' => 'required|numeric|exists:templates,id',
            'name' => 'required|min:3|max:20',
            'key' => 'required|min:3|max:200',
        ];
    }

    public function saveForm($id = null)
    {

        $this->key = $this->transliterate($this->name);
        $valideate = $this->validate();

        $templateOption = TemplateOption::find($this->id);

        if($templateOption)
        {
            $templateOption->update($valideate);
            $templateOption->save();
        }
        else
        {
            $templateOption = TemplateOption::create($valideate);
        }
        $this->reset();
        return $templateOption;
    }

    public function editForm($id)
    {
        $templateOption = TemplateOption::find($id);
        $this->fill($templateOption);
    }


    public function transliterate($st) {

        $st = strtr(strtolower($st), array(
            'а' => 'a', 'б' => 'b', 'в' => 'v',
            'г' => 'g', 'д' => 'd', 'е' => 'e',
            'ё' => 'e', 'ж' => 'zh', 'з' => 'z',
            'и' => 'i', 'й' => 'y', 'к' => 'k',
            'л' => 'l', 'м' => 'm', 'н' => 'n',
            'о' => 'o', 'п' => 'p', 'р' => 'r',
            'с' => 's', 'т' => 't', 'у' => 'u',
            'ф' => 'f', 'х' => 'h', 'ц' => 'c',
            'ч' => 'ch', 'ш' => 'sh', 'щ' => 'sch',
            'ь' => '\'', 'ы' => 'y', 'ъ' => '\'',
            'э' => 'e', 'ю' => 'yu', 'я' => 'ya',

            'А' => 'A', 'Б' => 'B', 'В' => 'V',
            'Г' => 'G', 'Д' => 'D', 'Е' => 'E',
            'Ё' => 'E', 'Ж' => 'Zh', 'З' => 'Z',
            'И' => 'I', 'Й' => 'Y', 'К' => 'K',
            'Л' => 'L', 'М' => 'M', 'Н' => 'N',
            'О' => 'O', 'П' => 'P', 'Р' => 'R',
            'С' => 'S', 'Т' => 'T', 'У' => 'U',
            'Ф' => 'F', 'Х' => 'H', 'Ц' => 'C',
            'Ч' => 'Ch', 'Ш' => 'Sh', 'Щ' => 'Sch',
            'Ь' => '\'', 'Ы' => 'Y', 'Ъ' => '\'',
            'Э' => 'E', 'Ю' => 'Yu', 'Я' => 'Ya',
            ' ' => '',
            '1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5', '6' => '6', '7' => '7', '8' => '8', '9' => '9', '0' => '0', 
        ));
        return $st;
    }
}
