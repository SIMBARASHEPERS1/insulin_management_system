<?php

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Livewire\Volt\Component;

new class extends Component {
    public User $user;

    public function mount(): void
    {
        $this->user->load(['patientInformation','patientAthrometric','patientPhysiology']);
    }
}; ?>

<div>
    <x-header :title="$user->name" separator>
        <x-slot:actions>
            <x-button label="View patient entries" link="/patient/{{ $user->id }}/entry" icon="o-eye" class="btn-primary" responsive/>
            <x-button label="Edit patient" link="/patient/{{ $user->id }}/edit" icon="o-pencil" class="btn-primary" responsive/>
            <x-button label="Delete patient" icon="o-trash" wire:click="delete" class="btn-error text-gray-100" wire:confirm="Are you sure?" spinner responsive />
            <x-button label="Back" link="/users" icon="o-arrow-uturn-left" responsive/>
        </x-slot:actions>
    </x-header>

    <div class="grid lg:grid-cols-2 gap-8">
        {{-- PERSONAL INFO --}}
        <x-card title="Personal Info" separator shadow>
            <div class="flex gap-4 text-sm">
                <x-avatar :image="$user->avatar" class="!w-20">
            </x-avatar>
            <div class="flex flex-col gap-2" >
                <div class="flex gap-2 text-sm">
                    <p class="font-bold">Phone:</p>
                    <p>{{$user->phone}} <span class="font-bold text-red-500">*</span> </p>
                </div>
                <div class="flex gap-2 text-sm">
                    <p class="font-bold">Email:</p>
                    <p>{{$user->email}}</p>
                </div>
                <div class="flex gap-2 text-sm">
                    <p class="font-bold">Physical address:</p>
                    <p>{{ $user->patientInformation->first()->address }}</p>
                </div>
            </div>
        </div>

        </x-card>

        {{-- DEMOGRAPHICS --}}
        <x-card title="Demographics" separator shadow>
            <div class="flex flex-col gap-2">
                <div class="flex gap-2 text-sm">
                    <p class="font-bold">Gender:</p>
                    <p>{{$user?->patientInformation->first()->gender ?? "Not Set"}}</p>
                </div>
                <div class="flex gap-2 text-sm">
                    <p class="font-bold">D.O.B:</p>
                    <p>
                        {{ Carbon::parse($user?->patientInformation->first()?->dob)->format('d-m-y') }}  ({{ Carbon::parse($user?->patientInformation->first()?->dob)->age . ' years' }})
                    </p>
                </div>
            </div>
        </x-card>
    </div>


    <div class="grid lg:grid-cols-2 gap-8 mt-8">
        {{-- ANTHROPOMETRY --}}
        <x-card title="Anthropometry" separator shadow>
            <div class="flex flex-col gap-2">
                <div class="flex gap-2 text-sm">
                    <p class="font-bold">Height:</p>
                    <p>{{$user?->patientAthrometric->first()->height.' cm'}}</p>
                </div>
                <div class="flex gap-2 text-sm">
                    <p class="font-bold">Weight:</p>
                    <p>{{$user?->patientAthrometric->first()->weight.' kg'}}</p>
                </div>
                <div class="flex gap-2 text-sm">
                    <p class="font-bold">BMI:</p>
                    <p>{{$user?->patientAthrometric->first()->bmi}} <span class="font-bold text-red-500">*</span></p>
                </div>
            </div>
        </x-card>

        {{-- PHYSIOLOGY --}}
        <x-card title="Physiology" separator shadow>
            <div class="grid grid-cols-2 gap-4">
                <div class="flex gap-2 text-sm">
                    <p class="font-bold">TBV:</p>
                    <p>{{$user?->patientPhysiology->first()->tbv. ' mL/kg'}}</p>
                </div>
                <div class="flex gap-2 text-sm">
                    <p class="font-bold">CBGR:</p>
                    <p>{{$user?->patientPhysiology->first()->cbgr. '/min'}}</p>
                </div>
                <div class="flex gap-2 text-sm">
                    <p class="font-bold">TDD:</p>
                    <p>50 <span class="font-bold text-red-500">*</span> </p>
                </div>
                <div class="flex gap-2 text-sm">
                    <p class="font-bold">ICR:</p>
                    <p>10 <span class="font-bold text-red-500">*</span> </p>
                </div>
                <div class="flex gap-2 text-sm">
                    <p class="font-bold">ISF:</p>
                    <p>{{$user?->patientPhysiology->first()->isf}}</p>
                </div>
                <div class="flex gap-2 text-sm">
                    <p class="font-bold">DIA:</p>
                    <p>{{$user?->patientPhysiology->first()->dia.' hrs'}}</p>
                </div>
            </div>
        </x-card>
    </div>

    <div class="mt-8" >
        {{-- EXERCISE INFO --}}
        <x-card title="Exercise Info" separator shadow >
            <div class="grid grid-cols-2 gap-4" >
                <div class="flex gap-2 text-sm">
                    <p class="font-bold">Heart Rate at Rest:</p>
                    <p>72 bpm <span class="font-bold text-red-500">*</span> </p>
                </div>
                <div class="flex gap-2 text-sm">
                    <p class="font-bold">Max heart rate:</p>
                    <p>176 bpm <span class="font-bold text-red-500">*</span> </p>
                </div>
                <div class="flex gap-2 text-sm">
                    <p class="font-bold">VO2 Max:</p>
                    <p>36.7 mL/kg/min <span class="font-bold text-red-500">*</span> </p>
                </div>
                <div class="flex gap-2 text-sm">
                    <p class="font-bold">METs Max:</p>
                    <p>10.5 <span class="font-bold text-red-500">*</span> </p>
                </div>
            </div>
        </x-card>
    </div>
</div>
