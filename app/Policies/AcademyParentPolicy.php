<?php

namespace App\Policies;

use App\Models\AcademyParent;
use App\Models\User;

class AcademyParentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('parent.manage') || $user->can('parent.view');
    }

    public function view(User $user, AcademyParent $academyParent): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $user->can('parent.manage');
    }

    public function update(User $user, AcademyParent $academyParent): bool
    {
        return $user->can('parent.manage');
    }

    public function delete(User $user, AcademyParent $academyParent): bool
    {
        return $user->can('parent.manage');
    }
}
