<?php

namespace App\Http\Controllers\Admin;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;
class StaffController extends BaseController
{
    /**
     * Display a listing of staff members.
     */
    public function index()
    {

        $staff = User::whereIn('role', ['admin', 'veterinarian', 'staff', 'reception', 'pharmacy'])->get();

        return view('admin.staff.index', compact('staff'));
    }

    /**
     * Show the form for creating a new staff member.
     */
    public function create()
    {
        return view('admin.staff.create');
    }

    public function store(Request $request){

        $staff = $request->validate([
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'role' => 'required|string|max:100',
            'address' => 'required|string|max:100',
            'contact_number' => 'required|string|max:20',
            'email' => 'required|string|email|max:255|unique:users,email',
            'username' => 'required|string|max:50|unique:users,username',
            'password' => 'required|string|min:6|confirmed'
        ],
        [
            'password.confirmed' => 'Password does not match'
        ]
        );

        $staff['password'] = bcrypt($staff['password']);
        
        $staff_create = User::create($staff);

        
        return redirect()->route('admin.staff.index')->with('success', 'Staff member created successfully.');
    }

    
    
   
    /**
     * Display the specified staff member.
     */
    public function show($id)
    {

        $member = User::findOrFail($id);
        
        return view('admin.staff.show', compact('member'));
    }

    /**
     * Show the form for editing the specified staff member.
     */
    public function edit($id)
    { 
        $staff = User::where('id', $id)->first();
        return view('admin.staff.edit', compact('staff'));
    }

    /**
     * Update the specified staff member in storage.
     */
    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'role' => 'required|string|max:100',
            'address' => 'required|string|max:100',
            'contact_number' => 'required|string|max:20',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($id)],
            'username' => ['required', 'string', 'max:50', Rule::unique('users')->ignore($id)],
            'password' => 'nullable|string|min:6|confirmed'
        ],
        [
            'password.confirmed' => 'Password does not match'
        ]);

          $user = User::findOrFail($id);

        if(empty($data['password'])){
            unset($data['password']);
        } else{ 
            $data['password'] = bcrypt($data['password']);
        }

      
        

      
        $user->update($data);

        return redirect()->route('admin.staff.index')->with('success', 'Staff member updated successfully.');
    }

    /**
     * Remove the specified staff member from storage.
     */
    public function destroy($id)
    {
       $user = User::findOrFail($id);
       $user->delete();
        return redirect()->route('admin.staff.index')->with('success', 'Staff member deleted successfully.');
    }

    public function filter(Request $request){
        
       $position = $request->position;
        $name = $request->name_filter;

         $staff = User::where('role', '<>', 'pet_owner');

        if(!empty($position)) {
            $staff = $staff->where('role', $position);
        }
      

      
       if(!empty($name)){
      $staff = $staff->where(DB::raw("CONCAT(first_name, ' ',last_name)"), "LIKE", "%$name%");
       
       }

        $staff = $staff->get();
       return view('admin.staff.index', compact('staff'));

      

      
    }
}
