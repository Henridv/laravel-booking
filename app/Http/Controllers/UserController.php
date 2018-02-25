<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

use App\User;

class UserController extends Controller
{
    /**
     * Save user
     */
    public function saveUser($request, $user)
    {
        $validatedData = $request->validate([
            'username' => ['required', Rule::unique('users')->ignore($user->id)],
            'name' => 'required',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'password' => 'sometimes|required|string|min:6|confirmed',
            'role' => 'sometimes|required|exists:roles,id',
        ]);

        $user->username = $request->get('username');
        $user->name = $request->get('name');
        $user->email = $request->get('email');

        if ($request->get('password')) {
            $user->password = bcrypt($request->get('password'));
        }

        if ($request->get('role')) {
            $user->role_id = $request->get('role');
        }

        $user->save();

        return $user;
    }

    /**
     * Callback for creating a new User
     */
    public function createUser(Request $request)
    {
        $this->saveUser($request, new User());

        return redirect()
            ->route('admin', ['#users'])
            ->with('success', 'User successfully added');
    }

    /**
     * Callback for updating a User
     */
    public function updateUser(Request $request, User $user)
    {
        $this->saveUser($request, $user);

        return redirect()
            ->route('admin', ['#users'])
            ->with('success', 'User successfully updated');
    }

    /**
     * Callback for updating a User
     */
    public function updateMe(Request $request)
    {
        $this->saveUser($request, Auth::user());

        return redirect()
            ->route('admin')
            ->with('success', 'Profile successfully updated');
    }
}
