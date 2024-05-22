<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Mary\Traits\Toast;

use function Livewire\Volt\rules;
use function Livewire\Volt\state;

state([
    'current_password' => '',
    'password' => '',
    'password_confirmation' => ''
]);

rules([
    'current_password' => ['required', 'string', 'current_password'],
    'password' => ['required', 'string', Password::defaults(), 'confirmed'],
]);

$updatePassword = function () {
    try {
        $validated = $this->validate();
    } catch (ValidationException $e) {
        $this->reset('current_password', 'password', 'password_confirmation');

        throw $e;
    }

    Auth::user()->update([
        'password' => Hash::make($validated['password']),
    ]);

    $this->reset('current_password', 'password', 'password_confirmation');

    $this->dispatch('password-updated');
};

?>

<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Update Password') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __('Ensure your account is using a long, random password to stay secure.') }}
        </p>
    </header>

    <form wire:submit="updatePassword" class="mt-6 space-y-6">
        <div>
            <x-input label="Current Password" wire:model="current_password" id="update_password_current_password"
                     name="current_password" type="password" class="mt-1 block w-full" autocomplete="current-password"/>
        </div>

        <div>
            <x-input label="New Password" wire:model="password" id="update_password_password" name="password"
                     type="password" class="mt-1 block w-full" autocomplete="new-password"/>
        </div>

        <div>
            <x-input label="Confirm Password" wire:model="password_confirmation"
                     id="update_password_password_confirmation" name="password_confirmation" type="password"
                     class="mt-1 block w-full" autocomplete="new-password"/>
        </div>

        <div class="flex items-center gap-4">
            <x-button label="Save Password" icon="o-paper-airplane" spinner="save" type="submit" class="btn-primary"/>

            <x-action-message class="me-3" on="password-updated">
                {{ __('Saved.') }}
            </x-action-message>
        </div>
    </form>
</section>
