<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\Role;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        $data['display_handle'] = User::normalizeDisplayHandle($data['display_handle'] ?? null);

        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'display_handle' => ['nullable', 'string', 'min:3', 'max:30', 'regex:/^[A-Za-z0-9._]+$/', 'unique:users,display_handle'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'school_level' => ['required', 'string', 'max:255'],
            'class_year' => ['required', 'string', 'max:255'],
            'country_of_study' => ['required', 'string', 'max:255'],
            'city_town' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        $user = User::create([
            'name' => $data['name'],
            'display_handle' => $data['display_handle'] ?? null,
            'email' => $data['email'],
            'country' => $data['country_of_study'],
            'state' => $data['country_of_study'],
            'county' => $data['city_town'],
            'level' => $data['school_level'],
            'grade' => $data['class_year'],
            'school' => 'Not provided',
            'school_level' => $data['school_level'],
            'class_year' => $data['class_year'],
            'country_of_study' => $data['country_of_study'],
            'city_town' => $data['city_town'],
            'password' => Hash::make($data['password']),
        ]);

        $role = Role::select('id')->where('name','user')->first();

        $user->roles()->attach($role);

        return $user;
    }

    protected function registered(Request $request, $user)
    {
        return redirect()->intended($this->redirectPath());
    }
}
