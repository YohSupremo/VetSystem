<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class UserController extends Controller
{
    private function isCustomerRole(User $user): bool
    {
        return in_array($user->role, ['registered_user', 'pet_owner'], true);
    }

    private function denyIfNotCustomerRole(User $user)
    {
        if (!$this->isCustomerRole($user)) {
            return redirect()->route('admin.users.index')->with('error', 'Only customer users are available in this module.');
        }

        return null;
    }

    private function hasAnchoredPets(User $user): bool
    {
        return $user->petOwner()->whereHas('pets')->exists();
    }

    private function resolveCustomerRole(User $user): string
    {
        return $this->hasAnchoredPets($user) ? 'pet_owner' : 'registered_user';
    }

    public function index(Request $request)
    {
        $users = QueryBuilder::for(User::class)
            ->whereIn('role', ['registered_user', 'pet_owner'])
            ->with(['petOwner.pets'])
            ->allowedFilters([
                AllowedFilter::callback('search', function ($query, $value) {
                    $term = trim((string) $value);

                    if ($term === '') {
                        return;
                    }

                    $query->where(function ($subQuery) use ($term) {
                        $subQuery->where('first_name', 'like', '%' . $term . '%')
                            ->orWhere('last_name', 'like', '%' . $term . '%')
                            ->orWhere('email', 'like', '%' . $term . '%')
                            ->orWhere('username', 'like', '%' . $term . '%')
                            ->orWhere('contact_number', 'like', '%' . $term . '%');
                    });
                }),
            ])
            ->orderByDesc('created_at')
            ->paginate(25)
            ->appends($request->query());

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
        // New customer accounts start as registered users until they have anchored pets.
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
        if ($redirect = $this->denyIfNotCustomerRole($user)) {
            return $redirect;
        }

        $user->loadMissing('petOwner.pets');

        return view('admin.users.show', compact('user'));
    }

    public function edit(User $user)
    {
        if ($redirect = $this->denyIfNotCustomerRole($user)) {
            return $redirect;
        }

        $user->loadMissing('petOwner.pets');

        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        if ($redirect = $this->denyIfNotCustomerRole($user)) {
            return $redirect;
        }

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
        // Keep non-customer roles unchanged; sync customer role from anchored pets.
        if (in_array($user->role, ['registered_user', 'pet_owner'], true)) {
            $data['role'] = $this->resolveCustomerRole($user);
        } else {
            $data['role'] = $user->role;
        }
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
        if ($redirect = $this->denyIfNotCustomerRole($user)) {
            return $redirect;
        }

        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'User deleted');
    }
}
