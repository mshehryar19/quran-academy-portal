<?php

namespace App\Policies;

use App\Models\ClassSlot;
use App\Models\User;

class ClassSlotPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('slot.manage');
    }

    public function view(User $user, ClassSlot $classSlot): bool
    {
        return $user->can('slot.manage');
    }

    public function create(User $user): bool
    {
        return $user->can('slot.manage');
    }

    public function update(User $user, ClassSlot $classSlot): bool
    {
        return $user->can('slot.manage');
    }

    public function delete(User $user, ClassSlot $classSlot): bool
    {
        return $user->can('slot.manage');
    }
}
