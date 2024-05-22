<?php

use App\Models\PatientActivity;
use App\Models\User;
use App\Traits\ClearsProperties;
use App\Traits\ResetsPaginationWhenPropsChanges;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Url;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination, ResetsPaginationWhenPropsChanges, ClearsProperties;

    #[Url]
    public array $sortBy = ['column' => 'name', 'direction' => 'asc'];

    public User $user;

    public function mount(): void
    {
        $this->user->load(['patientInformation', 'patientAthrometric', 'patientPhysiology']);
    }

    // All patients
    public function users(): LengthAwarePaginator
    {
        return PatientActivity::query()
            ->where('patient_id', $this->user->id)
            ->orderBy(...array_values($this->sortBy))
            ->paginate(10);
    }

    public function headers(): array
    {
        return [
            ['key' => 'name', 'label' => 'Protocol'],
            ['key' => 'last_entry', 'label' => 'Description', 'class' => 'hidden lg:table-cell'],
            ['key' => 'last_sugar', 'label' => 'Blood sugar Level', 'class' => 'hidden lg:table-cell'],
            ['key' => 'last_activity', 'label' => 'Time Stamp', 'class' => 'hidden'],
        ];
    }

    public function with(): array
    {
        return [
            'headers' => $this->headers(),
            'entries' => $this->users(),
        ];
    }
}; ?>

<div>
    {{--  HEADER  --}}
    <x-header title="Activity Log : {{$user->name}}" separator progress-indicator>
        <x-slot:actions>
{{--            <x-button label="View All Patients" link="/patients/view" icon="o-eye" class="btn-primary" responsive/>--}}
            <x-button label="View Patient" link="/patient/{{ $user->id }}" icon="o-arrow-uturn-left" responsive/>
        </x-slot:actions>
    </x-header>

    {{--  TABLE --}}
    <x-card>
        <x-table :headers="$headers" :rows="$entries" :sort-by="$sortBy" with-pagination/>

        @if(!$entries->count())
            <x-icon name="o-list-bullet" label="No Data Recorded." class="text-gray-400 mt-5"/>
        @endif
    </x-card>
</div>
