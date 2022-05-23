<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{

  use HandlesAuthorization;

  public function follow(User $currentUser, User $user)
  {
    #自己不能跟随自己
    return $currentUser->id !== $user->id;
  }

  public function update(User $currentUser, User $user)
  {
    return $currentUser->id === $user->id;
  }

  public function destroy(User $currentUser, User $user)
  {
    return $currentUser->is_admin && $currentUser->id !== $user->id;
  }
}
