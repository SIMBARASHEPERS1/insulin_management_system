<?php

use App\Actions\DeletePatientAction;
use App\Models\Country;
use App\Models\PatientHeartInformation;
use App\Models\User;
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
    public null|string $gender = null;

    #[Rule('required|date')]
    public string $dob = '';

    #[Rule('required|string')]
    public string $phone = '';

    #[Rule('required|numeric')]
    public float $height = 0.0;

    #[Rule('required|numeric')]
    public float $weight = 0.0;

    #[Rule('required|string')]
    public string $address = '';

    #[Rule('nullable|image|max:1024')]
    public $avatar_file;

    #[Rule('required|numeric')]
    public float $tdd = 0.0;

    #[Rule('required|numeric')]
    public float $dia = 0.0;

    #[Rule('required|numeric')]
    public float $hrr = 0.0;

    #[Rule('required|numeric')]
    public float $mhr = 0.0;


    public function mount(): void
    {
        $this->fill($this->user);
        $this->address = $this->user?->patientInformation->last()->address;
        $this->dob = $this->user?->patientInformation->last()->dob;
        $this->gender = $this->user?->patientInformation->last()->gender;
        $this->height = $this->user?->patientAthrometric->last()->height;
        $this->weight = $this->user?->patientAthrometric->last()->weight;
        $this->tdd = $this->user?->patientPhysiology->last()->tbv;
        $this->dia = $this->user?->patientPhysiology->last()->dia;
        $this->hrr = $this->user?->patientHeartInformation->last()->heart_rate;
        $this->mhr = $this->user?->patientHeartInformation->last()->mhr;
    }

    public function delete(): void
    {
        $action = new DeletePatientAction($this->user);
        $action->execute();

        $this->success('Deleted', redirectTo: '/patients/view');
    }

    public function save(): void
    {
        // Validate
        $data = $this->validate();
//        dd($data);

        $userDetails = [
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
        ];

        // Update
        $this->user->update($userDetails);

        //patient info
        $this->user->patientInformation()->create([
            'gender' => $data['gender'],
            'dob' => $data['dob'],
            'class' => Carbon\Carbon::parse($data['dob'])->age < 18 ? 'adolescent' : 'adult',
            'address' => $data['address'],
        ]);

        $bmi = $data['weight'] / ($data['height'] * $data['height']);

        $bmi_category = match (true) {
            $bmi <= 18 => 'Underweight',
            $bmi > 18 && $bmi <= 25 => 'Normal weight',
            $bmi > 25 && $bmi <= 30 => 'Overweight',
            default => 'Obese',
        };

        //patient anthropometric
        $this->user->patientAthrometric()->create([
            'height' => $data['height'],
            'weight' => $data['weight'],
            'bmi' => $bmi,
            'bmi_category' => $bmi_category,
        ]);

        $gender = $data['gender'];
        $heightCubed = $data['height'] * $data['height'] * $data['height'];
        $tbv = ($gender == 'male') ? (0.3669 * $heightCubed)
            + (0.03219 * $data['weight'])
            + 0.6041 : (0.3561 * $heightCubed)
            + (0.03308 * $data['weight'])
            + 0.1833;

        $this->user->patientPhysiology()->create([
            // 'tbv' => round($tbv, 5),
            'tbv' => $data['tdd'],
            'cbgr' => round((0.00556 / (0.6 * $tbv)), 5),
            'isf' => 2,
            'dia' => $data['dia'],
        ]);

        PatientHeartInformation::create([
            'patient_id' => $this->user->id,
            'heart_rate' => $data['hrr'],
            'mhr' => $data['mhr'],
        ]);

        if ($this->avatar_file) {
            $url = $this->avatar_file->store('patients', 'public');
            $this->user->update(['avatar' => "/storage/$url"]);
        }

        $this->success('Patient Updated With Success.', redirectTo: '/patient/' . $this->user->id . '/edit');
    }

}; ?>

<div>
    <x-header :title="$user->name" separator progress-indicator>
        <x-slot:actions>
            <x-button label="View patient entries" link="/patient/{{ $user->id }}/entry" icon="o-eye"
                      class="btn-primary" responsive/>
            <x-button label="Delete patient" icon="o-trash" wire:click="delete" class="btn-error text-gray-100"
                      wire:confirm="Are you sure?" spinner responsive/>
            <x-button label="Back" link="/patients/view" icon="o-arrow-uturn-left" responsive/>
        </x-slot:actions>
    </x-header>

    <div class="grid gap-5 lg:grid-cols-2">
        <div>
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
                    <x-input label="Address *" wire:model="address"/>
                    {{-- <x-select label="Country" wire:model="country_id" :options="$countries" placeholder="Select Country" /> --}}
                </x-card>


                <x-card title="Demographics" class="mt-8" separator shadow>
                    <x-select label="Gender *" wire:model="gender"
                              :options="collect([['id' => 'male', 'name' => 'Male'], ['id' => 'female', 'name' => 'Female']])"
                              placeholder="Select gender"
                              icon=""/>
                    <br>
                    <x-input label="D.O.B *" wire:model="dob"/>
                </x-card>

                <x-card title="Anthropometry" class="mt-8" separator shadow>
                    <x-input label="Height (cm) *" wire:model="height"/>
                    <br>
                    <x-input label="Weight (kg)*" wire:model="weight"/>
                </x-card>

                <x-card title="Physiology" class="mt-8" separator shadow>
                    <x-input label="TDD *" wire:model="tdd"/>
                    <br>
                    <x-input label="DIA *" wire:model="dia"/>
                </x-card>

                <x-card title="Exercise Info" class="mt-8" separator shadow>
                    <x-input label="HRR" wire:model="hrr"/>
                    <br>
                    <x-input label="Max heart rate" wire:model="mhr"/>
                </x-card>

                <x-slot:actions>
                    <x-button label="Cancel" link="/patient/{{$user->id}}"/>
                    <x-button label="Save" icon="o-paper-airplane" spinner="save" type="submit" class="btn-primary"/>
                </x-slot:actions>
            </x-form>
        </div>
        {{-- <div>
            <img src="/images/edit-form.png" width="300" class="mx-auto" />
        </div> --}}
    </div>
</div>
