<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AccountController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function edit()
    {
        return view('account.edit', [
            'user' => auth()->user(),
        ]);
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $request->merge([
            'display_handle' => User::normalizeDisplayHandle($request->input('display_handle')),
        ]);

        $validated = $request->validateWithBag('profile', [
            'name' => ['required', 'string', 'max:255'],
            'display_handle' => [
                'nullable',
                'string',
                'min:3',
                'max:30',
                'regex:/^[A-Za-z0-9._]+$/',
                Rule::unique('users', 'display_handle')->ignore($user->id),
            ],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
        ]);

        $emailChanged = $validated['email'] !== $user->email;

        if ($emailChanged) {
            $request->validateWithBag('profile', [
                'current_password' => ['required', 'current_password'],
            ]);

            $user->email = $validated['email'];
        }

        $user->name = $validated['name'];
        $user->display_handle = $validated['display_handle'] ?? null;
        $user->save();

        if ($emailChanged) {
            return redirect()
                ->route('account.edit')
                ->with('success', 'Email updated.');
        }

        return redirect()
            ->route('account.edit')
            ->with('success', 'Profile updated.');
    }

    public function updatePassword(Request $request)
    {
        $validated = $request->validateWithBag('passwordUpdate', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'confirmed', 'different:current_password'],
        ]);

        $request->user()->forceFill([
            'password' => Hash::make($validated['password']),
        ])->save();

        return redirect()
            ->route('account.edit')
            ->with('success', 'Password updated.');
    }
}
