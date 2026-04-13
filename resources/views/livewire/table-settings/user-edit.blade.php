<div class="modal fade"  wire:ignore.self id="userModalForm" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="userModalFormLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="userModalFormLabel">@if ($form->id) Изменить @else Создать @endif пользователя</h1>
            </div>
            <div class="modal-body">
                
                <form wire:submit="save">
                    <x-blocks.error-message />
                    <div class="mb-3">
                        <label for="name" class="form-label">Имя Фамилие</label>
                        <input type="text" wire:model="form.name" class="form-control" placeholder="Имя Фамилие" id="name" />
                        <div class="text-danger">@error('form.name') {{ $message }} @enderror</div>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">E-Mail</label>
                        <input type="text" wire:model="form.email" class="form-control" placeholder="Почта" id="email" />
                        <div class="text-danger">@error('form.email') {{ $message }} @enderror</div>
                    </div>
                    <div class="mb-3">
                        <label for="role" class="form-label">Роль</label>
                        <select class="form-select" wire:model="form.role" id="role">
                            <option value="{{App\Models\User::USER}}">{{App\Models\User::USER}}</option>
                            <option value="{{App\Models\User::ADMIN}}">{{App\Models\User::ADMIN}}</option>
                        </select>
                        <div class="text-danger">@error('form.role') {{ $message }} @enderror</div>
                    </div>
                    <div class="mb-3">
                        <label for="phone" class="form-label">Телефон</label>
                        <input type="text" wire:model="form.phone" class="form-control" placeholder="Телефон" id="phone" />
                        <div class="text-danger">@error('form.phone') {{ $message }} @enderror</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">Новый пароль</label>
                        <input type="password" wire:model="form.password" class="form-control" id="password">
                        <div class="text-danger">@error('form.password') {{ $message }} @enderror</div>
                    </div>

                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Подтверждение пароля</label>
                        <input type="password" wire:model="form.password_confirmation" class="form-control" id="password_confirmation">
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
                        <button type="submit" class="btn btn-primary">Сохранить</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>