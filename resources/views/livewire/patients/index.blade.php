<?php

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
    public string $name = '';

    public string $phone = '';

    #[Url]
    public ?int $country_id = 0;

    #[Url]
    public array $sortBy = ['column' => 'name', 'direction' => 'asc'];

    // All patients
    public function users(): LengthAwarePaginator
    {
        return User::query()
            ->where('status', 'active')
            ->where('role', 'patient')
            ->with(['country', 'orders.status'])
            ->withAggregate('country', 'name')
            ->when($this->name, fn(Builder $q) => $q->where('name', 'like', "%$this->name%"))
            ->when($this->phone, fn(Builder $q) => $q->where('phone', 'like', "%$this->phone%"))
            ->orderBy(...array_values($this->sortBy))
            ->paginate(7);
    }

    public function headers(): array
    {
        return [
            ['key' => 'avatar', 'label' => '', 'class' => 'w-14', 'sortable' => false],
            ['key' => 'name', 'label' => 'Name'],
            ['key' => 'last_entry', 'label' => 'Last entry date', 'sortBy' => 'country_name', 'class' => 'hidden lg:table-cell'],
            ['key' => 'last_sugar', 'label' => 'Blood sugar', 'class' => 'hidden lg:table-cell'],
            ['key' => 'last_activity', 'label' => 'Last activity', 'class' => 'hidden lg:table-cell'],
            ['key' => 'phone', 'label' => 'Phone', 'class' => 'hidden lg:table-cell']
        ];
    }

    public function with(): array
    {
        return [
            'headers' => $this->headers(),
            'patients' => $this->users(),
        ];
    }
}; ?>

<div>
    {{--  HEADER  --}}
    <x-header title="Patients" separator progress-indicator>
        {{--  SEARCH --}}
        <x-slot:middle class="!justify-end">
            <x-input placeholder="Search patient" wire:model.live.debounce="name" icon="o-magnifying-glass" clearable/>
        </x-slot:middle>

        {{-- ACTIONS  --}}
        <x-slot:actions>
            <x-button label="New Patient" icon="o-plus" link="/patient/create" class="btn-primary" responsive/>
        </x-slot:actions>
    </x-header>

    {{--  TABLE --}}
    <x-card>
        <x-table :headers="$headers" :rows="$patients" :sort-by="$sortBy" link="/patient/{id}" with-pagination>
            {{-- Avatar scope --}}
            @scope('cell_avatar', $user)
              <x-avatar :image="$user->avatar" class="!w-10"/>
            @endscope
        </x-table>
    </x-card>
</div>
