<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function index()
    {
        $users = User::orderBy('created_at', 'desc')->paginate(25);
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $rules = [
            'username' => 'required|string|max:100|unique:users,username',
            'email' => 'required|email|max:150|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'contact_number' => 'required|string|max:20',
            'address' => 'required|string',
            'is_active' => 'nullable|boolean',
            'email_verified' => 'nullable|boolean',
            'phone_verified' => 'nullable|boolean',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $validator->validated();
        // Force role to registered_user regardless of input
        $data['role'] = 'registered_user';
        $data['password'] = Hash::make($data['password']);
        $data['is_active'] = isset($data['is_active']) ? (int)$data['is_active'] : 1;
        $data['email_verified'] = isset($data['email_verified']) ? (int)$data['email_verified'] : 0;
        $data['phone_verified'] = isset($data['phone_verified']) ? (int)$data['phone_verified'] : 0;

        User::create($data);

        return redirect()->route('admin.users.index')->with('success', 'User created');
    }

    public function show(User $user)
    {
        return view('admin.users.show', compact('user'));
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $rules = [
            'username' => 'required|string|max:100|unique:users,username,' . $user->id,
            'email' => 'required|email|max:150|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:6|confirmed',
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'contact_number' => 'required|string|max:20',
            'address' => 'required|string',
            'is_active' => 'nullable|boolean',
            'email_verified' => 'nullable|boolean',
            'phone_verified' => 'nullable|boolean',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $validator->validated();
        // ensure role remains registered_user
        $data['role'] = 'registered_user';
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $data['is_active'] = isset($data['is_active']) ? (int)$data['is_active'] : 0;
        $data['email_verified'] = isset($data['email_verified']) ? (int)$data['email_verified'] : 0;
        $data['phone_verified'] = isset($data['phone_verified']) ? (int)$data['phone_verified'] : 0;

        $user->update($data);

        return redirect()->route('admin.users.index')->with('success', 'User updated');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'User deleted');
    }
}
