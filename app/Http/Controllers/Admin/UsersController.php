<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Role;
use Illuminate\Validation\Rule;
use Gate;

class UsersController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::with('roles')->orderBy('name')->get();
        return view('admin.users.index')->with('users', $users);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        if(Gate::denies('edit-users'))
        {
            return redirect()->route('users.index');
        }
        
        $roles = Role::all();

        return view('admin.users.edit')->with([
            'user' => $user,
            'roles' => $roles,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        if(Gate::denies('edit-users'))
        {
            return redirect()->route('users.index');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'roles' => ['array'],
            'roles.*' => ['exists:roles,id'],
        ]);

        $user->roles()->sync($validated['roles'] ?? []);

        $user->name = $validated['name'];
        $user->email = $validated['email'];

        if($user->save())
        {
            $request->session()->flash('success', $user->name . ' updated');
        }else{
            $request->session()->flash('error', $user->name . ' not updated');
        }
        

        return redirect()->route('users.index');
    }

    public function editEmail(User $user)
    {
        return view('admin.users.recover-email')->with('user', $user);
    }

    public function updateEmail(Request $request, User $user)
    {
        $validated = $request->validate([
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
        ]);

        $user->email = $validated['email'];

        if($user->save())
        {
            $request->session()->flash('success', $user->name . ' email changed.');
        }else{
            $request->session()->flash('error', $user->name . ' email was not changed');
        }

        return redirect()->route('users.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        if(Gate::denies('delete-users'))
        {
            return redirect()->route('users.index');
        }

        $user->roles()->detach();
        $user->delete();

        return redirect()->route('users.index')->with('success', $user->name . ' deleted.');
    }
}
