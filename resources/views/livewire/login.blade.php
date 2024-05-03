<?php

use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Volt\Component;

new
#[Layout('components.layouts.empty')]
#[Title('Login')]
class extends Component {
    public function login()
    {
        if (auth()->user()) {
            return redirect('/');
        }

        // Creates a new user on every login to avoid session collision
        // Just for demo purposes.
        $user = User::factory()->create();

        auth()->login($user);
        request()->session()->regenerate();

        return redirect()->intended();
    }
}; ?>

<div class="mt-20 md:w-96 mx-auto">
    <x-flow-brand class="mb-8" />

    <x-form wire:submit="login">
        <x-input label="E-mail" value="random@random.com" icon="o-envelope" inline />
        <x-input label="Password" value="random" type="password" icon="o-key" inline />

        <x-slot:actions>
            <x-button label="Login" type="submit" icon="o-paper-airplane" class="btn-primary" spinner="login" />
        </x-slot:actions>
    </x-form>
</div>

