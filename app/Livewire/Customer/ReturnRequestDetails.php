<?php

namespace App\Livewire\Customer;

use App\Models\ReturnItem;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.shop')]
#[Title('Return Request Details')]
class ReturnRequestDetails extends Component
{
    public $returnRequest;

    public function mount(ReturnItem $returnRequest)
    {
        $this->returnRequest = $returnRequest;
    }

    public function getStatusBadgeColor(string $status): string
    {
        return match ($status) {
            'requested' => 'yellow',
            'approved' => 'green',
            'rejected' => 'red',
            'received' => 'blue',
            default => 'zinc',
        };
    }

    public function render()
    {
        return view('livewire.customer.return-request-details');
    }
}
