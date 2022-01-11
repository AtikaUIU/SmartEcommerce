<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\Division;
use App\Models\District;
use Illuminate\Support\Str;
use App\Notifications\VerifyRegistration; 
use Alert;
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
    // protected $redirectTo = RouteServiceProvider::HOME;
    protected $redirectTo = '/';
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
 * @override
 * showRegistrationForm
 *
 * Display the registration form
 *
 * @return void view
 */
    public function showRegistrationForm()
    {
         $divisions = Division::orderBy('priority', 'asc')->get();
         $districts = District::orderBy('name', 'asc')->get();

         return view('auth.register', compact('districts', 'divisions'));
        // return view('auth.register');
    }
    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            // 'name' => ['required', 'string', 'max:255'],
            // 'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            // 'password' => ['required', 'string', 'min:8', 'confirmed'],

              'first_name' => 'required|string|max:30',
              'last_name' => 'nullable|string|max:15',
              'email' => 'required|string|email|max:100|unique:users',
              'password' => 'required|string|min:6|confirmed',
              'division_id' => 'required|numeric',
              'district_id' => 'required|numeric',
              'phone_no' => 'required|max:15',
              'street_address' => 'required|max:100',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function register(Request $request)
     {
        $user = User::create([
          'first_name' => $request->first_name,
          'last_name' => $request->last_name,
          'username' => Str::slug($request->first_name.$request->last_name),
          'division_id' => $request->division_id,
          'district_id' => $request->district_id,
          'phone_no' => $request->phone_no,
          'street_address' => $request->street_address,
          'ip_address' => request()->ip(),
          'email' => $request->email,
          'password' => Hash::make($request->password),
          'remember_token'  =>Str::random(50),
          'status'  => 0,
        ]);

        $user->notify(new VerifyRegistration($user));

        Alert::toast('A confirmation email has sent to you.. Please check and confirm your email', 'success');
        return redirect('/');


   }
}