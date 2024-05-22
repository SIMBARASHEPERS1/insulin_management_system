<?php

namespace App\Actions;

use App\Exceptions\AppException;
use App\Models\User;

class DeletePatientAction
{
    public function __construct(private User $user)
    {
    }

    public function execute(): void
    {
        $this->user->update(['status' => 'inactive']);
    }
}
