<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Rule;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use Mary\Traits\Toast;

new class extends Component {
    use Toast, WithFileUploads;

    public User $user;

    #[Rule('required')]
    public string $name = '';

    #[Rule('required|email')]
    public string $email = '';

    #[Rule('required|string')]
    public $gender = null;

    #[Rule('required|date')]
    public string $dob;

    #[Rule('required|numeric')]
    public float $height;

    #[Rule('required|numeric')]
    public float $weight;

    #[Rule('required|string')]
    public string $address;

    #[Rule('nullable|string')]
    public string $phone = '';

    #[Rule('nullable|image|max:1024')]
    public $avatar_file;

    public function mount(): void
    {
        $this->user = User::find(Auth::user()->id);
        $this->fill($this->user);
        $this->address = $this->user?->patientInformation->first()->address ?? "No Address";

        if ((auth()->user()->role === 'patient')) {
            $this->gender = $this->user->patientInformation->first()->gender;
            $this->dob = $this->user->patientInformation->first()->dob;
            $this->height = $this->user->patientAthrometric->first()->height;
            $this->weight = $this->user->patientAthrometric->first()->weight;
        }
    }

    public function save(): void
    {
        // Validate
        $data = $this->validate();

        // Update
        $this->user->update($data);

        if ($this->avatar_file) {
            $url = $this->avatar_file->store('patients', 'public');
            $this->user->update(['avatar' => "/storage/$url"]);
        }

        $this->success('Customer updated with success.', redirectTo: '/patients');
    }

}; ?>

<div>
    <x-header title="Settings" separator progress-indicator>
    </x-header>
    <div class="grid gap-5 lg:grid-cols-2">
        <x-form wire:submit="save">
            <x-card title="Personal Info" separator shadow>
                <x-file label="Profile picture" wire:model="avatar_file" accept="image/png, image/jpeg"
                        hint="Click to change | Max 1MB" crop-after-change>
                    <img src="{{ $user->avatar ?? '/images/empty-user.jpg' }}" class="h-40 rounded-lg mb-3"/>
                </x-file>
                <br>
                <x-input label="Name" wire:model="name"/>
                <br>
                <x-input label="Phone *" wire:model="phone"/>
                <br>
                <x-input label="Email" wire:model="email"/>
                <br>
                <x-input label="Address" wire:model="address"/>
                {{-- <x-select label="Country" wire:model="country_id" :options="$countries" placeholder="Select Country" /> --}}
            </x-card>

{{--            @if(auth()->user()->role === 'patient')--}}
{{--                <x-card title="Demographics" class="mt-8" separator shadow>--}}
{{--                    <x-select label="Gender" wire:model="gender"--}}
{{--                              :options="collect([['id' => 'male', 'name' => 'Male'], ['id' => 'female', 'name' => 'female']])"--}}
{{--                              placeholder="Select gender"--}}
{{--                              icon=""/>--}}
{{--                    <br>--}}
{{--                    <x-input label="D.O.B" wire:model="dob"/>--}}
{{--                </x-card>--}}

{{--                <x-card title="Anthropometry" class="mt-8" separator shadow>--}}
{{--                    <x-input label="Height (cm)" wire:model="height"/>--}}
{{--                    <br>--}}
{{--                    <x-input label="Weight (kg)" wire:model="weight"/>--}}
{{--                </x-card>--}}

{{--                <x-card title="Exercise Info" class="mt-8" separator shadow>--}}
{{--                    <x-input label="HRR" wire:model="hrr"/>--}}
{{--                    <br>--}}
{{--                    <x-input label="Max heart rate" wire:model="mhr"/>--}}
{{--                    <br>--}}
{{--                    <p class="text-xs">Don't know how to determine HRR or MHR? <span--}}
{{--                            class="underline cursor-pointer text-primary">Click here</span></p>--}}
{{--                </x-card>--}}
{{--            @endif--}}

            <x-slot:actions>
                <x-button label="Cancel" link="/users"/>
                <x-button label="Save" icon="o-paper-airplane" spinner="save" type="submit" class="btn-primary"/>
            </x-slot:actions>
        </x-form>

        @if(auth()->user()->role === 'admin')
            <div>
                <x-card title="Admin settings" separator shadow>
                </x-card>
            </div>
        @endif

        <x-card title="Password" separator shadow>
            <livewire:userSettings.password_change/>
        </x-card>
    </div>
</div>
