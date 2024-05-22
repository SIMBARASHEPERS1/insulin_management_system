<?php

use App\Livewire\Forms\LoginForm;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Volt\Component;

new
#[Layout('components.layouts.empty')]
#[Title('Login')]
class extends Component {

    public LoginForm $form;

    public function login()
    {
        $this->validate();

        $this->form->authenticate();

        \Illuminate\Support\Facades\Session::regenerate();

//        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);

        return redirect()->intended();
    }
}; ?>

<div class="mt-20 md:w-96 mx-auto">
    <x-flow-brand class="mb-8"/>

    <x-form wire:submit="login">
        <x-input label="E-mail" wire:model="form.email" id="email" name="email" required  icon="o-envelope" inline/>
{{--        <x-errors :messages="$errors->get('form.email')" class="mt-2" />--}}

        <x-input label="Password" wire:model="form.password" id="password" type="password" name="password"
                 required  icon="o-key" inline/>

{{--        <x-errors :messages="$errors->get('form.password')" class="mt-2" />--}}

        <x-slot:actions>
            <x-button label="Login" type="submit" icon="o-paper-airplane" class="btn-primary" spinner="login"/>
        </x-slot:actions>
    </x-form>
</div>

