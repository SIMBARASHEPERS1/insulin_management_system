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

    public function favorites(): Collection
    {
        return OrderItem::query()
            ->with('product.category')
            ->selectRaw("count(1) as amount, product_id")
            ->whereRelation('order', 'user_id', $this->user->id)
            ->groupBy('product_id')
            ->orderByDesc('amount')
            ->take(2)
            ->get()
            ->transform(function (OrderItem $item) {
                $product = $item->product;
                $product->amount = $item->amount;

                return $product;
            });
    }

    public function orders(): Collection
    {
        return Order::with(['user', 'status'])
            ->where('user_id', $this->user->id)
            ->latest('id')
            ->take(5)
            ->get();
    }

    public function headers(): array
    {
        return [
            ['key' => 'id', 'label' => '#'],
            ['key' => 'date_human', 'label' => 'Date'],
            ['key' => 'total_human', 'label' => 'Total'],
            ['key' => 'status', 'label' => 'Status', 'class' => 'hidden lg:table-cell']
        ];
    }

    public function with(): array
    {
        return [
            'favorites' => $this->favorites(),
            'orders' => $this->orders(),
            'headers' => $this->headers()
        ];
    }
}; ?>

<div>
    <x-header :title="$user->name" separator>
        <x-slot:actions>
            <x-button label="Edit" link="/users/{{ $user->id }}/edit" icon="o-pencil" class="btn-primary" responsive/>
        </x-slot:actions>
    </x-header>

    <div class="grid lg:grid-cols-2 gap-8">
        {{-- INFO --}}
        <x-card title="Info" separator shadow>
            <x-avatar :image="$user->avatar" class="!w-20">
                <x-slot:title class="pl-2">
                    {{ $user->name }}
                </x-slot:title>
                <x-slot:subtitle class="flex flex-col gap-2 p-2 pl-2">
                    <x-icon name="o-envelope" :label="$user->email"/>
                    <x-icon name="o-folder-minus"
                            :label="Carbon::parse($user?->patientInformation->first()?->dob)->age . ' years'"/>
                    <x-icon name="o-user-plus"
                            :label="$user?->patientInformation->first()?->class"/>
                </x-slot:subtitle>
            </x-avatar>
        </x-card>

        {{-- FAVORITES --}}
        <x-card title="Information" separator shadow>
            <div class="grid grid-cols-2 gap-4 content-start">
                <div>
                    <x-list-item :item="$user?->patientInformation->first()" sub-value="category.name" avatar="cover"
                                 no-separator>
                        <x-slot:value>
                            {{ __('BMI : ').$user?->patientAthrometric->first()->bmi}}
                        </x-slot:value>
                    </x-list-item>
                    <x-list-item :item="$user?->patientPhysiology->first()" sub-value="category.name" avatar="cover"
                                 no-separator>
                        <x-slot:value>
                            {{ __('TBV : ').$user?->patientPhysiology->first()->tbv}}
                        </x-slot:value>
                    </x-list-item>
                    <x-list-item :item="$user?->patientPhysiology->first()" sub-value="category.name" avatar="cover"
                                 no-separator>
                        <x-slot:value>
                            {{ __('CBGR : ').$user?->patientPhysiology->first()->cbgr}}
                        </x-slot:value>
                    </x-list-item>
                </div>
                <div>
                    <x-list-item :item="$user?->patientPhysiology->first()" sub-value="category.name" avatar="cover"
                                 no-separator>
                        <x-slot:value>
                            {{ __('ISF : ').$user?->patientPhysiology->first()->isf}}
                        </x-slot:value>
                    </x-list-item>
                    <x-list-item :item="$user?->patientPhysiology->first()" sub-value="category.name" avatar="cover"
                                 no-separator>
                        <x-slot:value>
                            {{ __('DIA : ').$user?->patientPhysiology->first()->dia}}
                        </x-slot:value>
                    </x-list-item>
                </div>
            </div>
        </x-card>
    </div>

    {{-- RECENT ORDERS --}}
    <x-card title="Recent Data" separator shadow class="mt-8">
        <x-table :rows="$orders" :headers="$headers" link="/orders/{id}/edit">
            @scope('cell_status', $order)
            <x-badge :value="$order->status->name" :class="$order->status->color"/>
            @endscope
        </x-table>

        @if(!$orders->count())
            <x-icon name="o-list-bullet" label="Nothing here." class="text-gray-400 mt-5"/>
        @endif
    </x-card>
</div>
