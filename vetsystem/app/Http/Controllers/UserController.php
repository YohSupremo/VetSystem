<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
class UserController extends Controller
{
    public function register(Request $request){

        $getData = $request->validate([
            'last_name' => 'required|string|max:50',
            'first_name' => 'required|string|max:50',
            'age' => 'required|integer',
            'contact_number' => 'required|string|max:20',
            'username' => 'required|string|max:50|unique:users,username',
            'password' => 'required|string|min:6'
        ]);

       $getData['password'] = bcrypt($getData['password']);
       User::create($getData);
       return redirect('/login');
    }

    public function login(Request $request){

        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string'
        ]);

        $user = User::where('username', $credentials['username'])->first();

        if($user && Hash::check($credentials['password'], $user->password)){

        return redirect('/dashboard');
        } else {
              return back()->withErrors(['username' => 'Invalid username or password']);
        }
    }
}
