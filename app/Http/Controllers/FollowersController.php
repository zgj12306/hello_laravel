<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FollowersController extends Controller
{
    /**
     * FollowersController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * @param User $user
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(User $user)
    {
        $this->authorize('follow', $user);

        if (!Auth::user()->isFollowing($user->id)) {
            Auth::user()->follow($user->id);
        }

        return redirect()->route('users.show', $user->id);
    }

    /**
     * @param User $user
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy(User $user)
    {
        $this->authorize('follow', $user);

        if (Auth::user()->isFollowing($user->id)) {
            Auth::user()->unfollow($user->id);
        }

        return redirect()->route('users.show', $user->id);
    }
}
