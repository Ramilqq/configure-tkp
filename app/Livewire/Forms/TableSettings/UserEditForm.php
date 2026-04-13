<?php

namespace App\Livewire\Forms\TableSettings;

use App\Models\User;
use Livewire\Attributes\Validate;
use Livewire\Form;
use App\Rules\EmailDomain;
use Illuminate\Support\Facades\Hash;

class UserEditForm extends Form
{
    public ?int $id = null;
    public string $name = '';
    public string $email = '';
    public string $role = 'user';
    public string $phone = '';

    public string $password = '';
    public string $password_confirmation = '';

    // Правила валидации для полей формы
    protected function editRules()
    {
        return [
            'name' => 'required|string|min:1|max:255',
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $this->id, new EmailDomain],
            'role' => 'required|string|min:1|max:255',
            'phone' => 'required|string|min:1|max:255',
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ];
    }
    protected function createRules()
    {
        return [
            'name' => 'required|string|min:1|max:255',
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $this->id, new EmailDomain],
            'role' => 'required|string|min:1|max:255',
            'phone' => 'required|string|min:1|max:255',
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }
    // Метод для сохранения изменений в базе данных
    public function saveForm()
    {
        $this->id ? $rules = $this->editRules() : $rules = $this->createRules();
        $valideate = $this->validate($rules);
        
        if ($valideate['password']){
            $valideate['password'] = Hash::make($valideate['password']);
        } else {
            unset($valideate['password']);
        }
        
        $user = User::updateOrCreate(
            ['id' => $this->id],
            $valideate
        );

        $this->fill($user);
    }
    // Метод для создания новой записи в базе данных 
    public function createForm()
    {
        $this->reset();
        $this->resetValidation();
        
        $this->fill($this);
    }
    // Метод для заполнения формы данными из базы данных
    public function editForm($id = null)
    {
        $this->resetValidation();
        $user = User::find($id);
        
        $this->fill($user);
        $this->reset(['password', 'password_confirmation']);
    }
}
