<?php

use App\Actions\DeleteCategoryAction;
use App\Exceptions\AppException;
use App\Models\Category;
use App\Traits\ResetsPaginationWhenPropsChanges;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Livewire\Attributes\On;
use Livewire\Volt\Component;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use Mary\Traits\Toast;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Carbon\Carbon;

new class extends Component {
    use Toast, WithPagination, ResetsPaginationWhenPropsChanges;

    public int $totalPatients = 0;
    public int $totalAdults = 0;
    public int $totalAdolescents = 0;
    public int $totalHypoglycemic = 0;
    public int $totalHyperglycemic = 0;
    public int $totalCriticalLevels = 0;

    public function mount()
    {
        $dateEighteenYearsAgo = Carbon::now()->subYears(18);
        $this->totalPatients = User::where('role', 'patient')->count();
        $this->totalAdults = User::where('role', 'patient')
            ->whereRelation('patientInformation', function ($query) use ($dateEighteenYearsAgo) {
                $query->where('dob', '<=', $dateEighteenYearsAgo);
            })->count();

        $this->totalAdolescents = User::where('role', 'patient')
            ->whereRelation('patientInformation', function ($query) use ($dateEighteenYearsAgo) {
                $query->where('dob', '>', $dateEighteenYearsAgo);
            })->count();
    }

}; ?>

<div>
    {{--  HEADER  --}}
    <x-header title="Analytics" separator progress-indicator>
        {{-- <x-select :options="$periods" wire:model.live="period" icon="o-calendar"/> --}}
    </x-header>

    @if(auth()->user()->role === 'admin')
        {{-- PATIENTS --}}
        <div class="flex flex-col gap-8 mt-8">
            <div>
                <x-card title="Patients" separator shadow>
                    <div class="grid grid-cols-3 gap-4 content-start">
                        <div
                            class="px-5 py-4  cursor-pointer hover:bg-gray-100 hover:bg-opacity-5 rounded duration-200">
                            <div>
                                <p class="text-xs text-gray-500">Total Patients</p>
                                <p class="text-xl font-black">{{$totalPatients}}</p>
                            </div>
                        </div>
                        <div
                            class="px-5 py-4  cursor-pointer hover:bg-gray-100 hover:bg-opacity-5 rounded duration-200">
                            <div>
                                <p class="text-xs text-gray-500">Total Adults</p>
                                <p class="text-xl font-black">{{$totalAdults}}</p>
                            </div>
                        </div>
                        <div
                            class="px-5 py-4  cursor-pointer hover:bg-gray-100 hover:bg-opacity-5 rounded duration-200">
                            <div>
                                <p class="text-xs text-gray-500">Total Adolescents</p>
                                <p class="text-xl font-black">{{$totalAdolescents}}</p>
                            </div>
                        </div>
                        <div
                            class="px-5 py-4  cursor-pointer hover:bg-gray-100 hover:bg-opacity-5 rounded duration-200">
                            <div>
                                <p class="text-xs text-gray-500">Total Hypoglycemic</p>
                                <p class="text-xl font-black">0</p>
                            </div>
                        </div>
                        <div
                            class="px-5 py-4  cursor-pointer hover:bg-gray-100 hover:bg-opacity-5 rounded duration-200">
                            <div>
                                <p class="text-xs text-gray-500">Total Hyperglycemia</p>
                                <p class="text-xl font-black">0</p>
                            </div>
                        </div>
                        <div
                            class="px-5 py-4  cursor-pointer hover:bg-gray-100 hover:bg-opacity-5 rounded duration-200">
                            <div>
                                <p class="text-xs text-gray-500">Total Critical Levels</p>
                                <p class="text-xl font-black">0</p>
                            </div>
                        </div>
                    </div>
                </x-card>
            </div>

            {{-- ADMINISTRATORS --}}
            <div>
                <x-card title="Administrators" separator shadow>
                    <div class="grid grid-cols-3 gap-4 content-start">
                        <div
                            class="px-5 py-4  cursor-pointer hover:bg-gray-100 hover:bg-opacity-5 rounded duration-200">
                            <div>
                                <p class="text-xs text-gray-500">Total Administrators</p>
                                <p class="text-xl font-black">0</p>
                            </div>
                        </div>
                        <div
                            class="px-5 py-4  cursor-pointer hover:bg-gray-100 hover:bg-opacity-5 rounded duration-200">
                            <div>
                                <p class="text-xs text-gray-500">Total Clinical</p>
                                <p class="text-xl font-black">0</p>
                            </div>
                        </div>
                        <div
                            class="px-5 py-4  cursor-pointer hover:bg-gray-100 hover:bg-opacity-5 rounded duration-200">
                            <div>
                                <p class="text-xs text-gray-500">Total Clerical</p>
                                <p class="text-xl font-black">0</p>
                            </div>
                        </div>
                    </div>
                </x-card>
            </div>
        </div>
    @else
        <div>
            <x-card title="Activities" separator shadow>
                <div class="grid grid-cols-2 gap-4">
                    <div class="p-4">
                        <div>
                            <p class="text-xs text-gray-500">Total Eating</p>
                            <p class="text-xl font-black">26</p>
                            <div class=" col-span-6 lg:col-span-3">
                                <livewire:categories.chart-eating :$period/>
                            </div>
                        </div>
                    </div>
                    <div class="p-4">
                        <div>
                            <p class="text-xs text-gray-500">Total Exercising</p>
                            <p class="text-xl font-black">15</p>
                            <div class=" col-span-6 lg:col-span-3">
                                <livewire:categories.chart-eating :$period/>
                            </div>
                        </div>
                    </div>
                </div>
            </x-card>
        </div>
    @endif
</div>
