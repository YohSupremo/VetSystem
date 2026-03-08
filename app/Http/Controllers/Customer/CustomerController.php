<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\PetOwner;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CustomerController extends Controller
{
    public function profile(Request $request)
    {
        // Get the authenticated user from session (matching existing login system)
        $username = session('username');
        if (!$username) {
            return redirect('/login')->with('error', 'Please login first');
        }
        
        $user = User::where('username', $username)->first();
        if (!$user || ($user->role !== 'pet_owner' && $user->role !== 'registered_user')) {
            return redirect('/login')->with('error', 'Access denied');
        }

        $petOwner = PetOwner::where('user_id', $user->id)->first();
        
        return view('customer.profile', compact('user', 'petOwner'));
    }
    
    public function updateProfile(Request $request)
    {
        $expectsJson = $request->expectsJson() || $request->ajax();

        // Get the authenticated user from session
        $username = session('username');
        if (!$username) {
            if ($expectsJson) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please login first',
                ], 401);
            }
            return redirect('/login')->with('error', 'Please login first');
        }
        
        $user = User::where('username', $username)->first();
        if (!$user || ($user->role !== 'pet_owner' && $user->role !== 'registered_user')) {
            if ($expectsJson) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied',
                ], 403);
            }
            return redirect('/login')->with('error', 'Access denied');
        }

        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'username' => [
                'required',
                'string',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'contact_number' => 'required|string|max:20',
            'address' => 'required|string|max:255',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'emergency_contact_name' => 'nullable|string|max:150',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'emergency_contact_relationship' => 'nullable|in:spouse,parent,sibling,relative,friend,neighbor,other',
        ]);

        if ($validator->fails()) {
            if ($expectsJson) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first(),
                    'errors' => $validator->errors(),
                ], 422);
            }

            return redirect()->back()->withErrors($validator)->withInput();
        }
        
        // Update user information
        $updateData = [
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'username' => $request->username,
            'contact_number' => $request->contact_number,
            'address' => $request->address,
        ];
        
        // Handle profile picture upload
        $profilePictureUpdated = false;
        if ($request->hasFile('profile_picture')) {
            try {
                $file = $request->file('profile_picture');
                
                // Check if file is valid
                if (!$file->isValid()) {
                    if ($expectsJson) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Invalid file upload'
                        ], 422);
                    }
                    return redirect()->back()->with('error', 'Invalid file upload');
                }
                
                // Ensure storage directory exists
                $storagePath = storage_path('app/public/profile_pictures');
                if (!file_exists($storagePath)) {
                    mkdir($storagePath, 0755, true);
                }
                
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('profile_pictures', $filename, 'public');
                
                if ($path) {
                    $updateData['profile_picture'] = $path;
                    $profilePictureUpdated = true;
                } else {
                    if ($expectsJson) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Failed to store file'
                        ], 500);
                    }
                    return redirect()->back()->with('error', 'Failed to store file');
                }
            } catch (\Exception $e) {
                if ($expectsJson) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Upload failed: ' . $e->getMessage()
                    ], 500);
                }
                return redirect()->back()->with('error', 'Upload failed: ' . $e->getMessage());
            }
        }
        
        $user->update($updateData);

        $petOwner = PetOwner::firstOrCreate(
            ['user_id' => $user->id],
            ['notes' => null]
        );

        $petOwner->update([
            'emergency_contact_name' => $request->input('emergency_contact_name') ?: null,
            'emergency_contact_phone' => $request->input('emergency_contact_phone') ?: null,
            'emergency_contact_relationship' => $request->input('emergency_contact_relationship') ?: null,
        ]);
        
        // Refresh the user model to get updated data
        $user->refresh();

        // Keep session auth key in sync if username changed.
        session(['username' => $user->username]);
        
        // Return JSON response for AJAX requests
        if ($expectsJson) {
            $response = [
                'success' => true,
                'message' => 'Profile updated successfully!',
                'profile_picture_updated' => $profilePictureUpdated
            ];
            
            if ($profilePictureUpdated && isset($updateData['profile_picture'])) {
                $response['profile_picture_url'] = asset('storage/' . $updateData['profile_picture']) . '?t=' . time();
            }
            
            return response()->json($response);
        }
        
        return redirect()->route('customer.profile')
            ->with('success', 'Profile updated successfully!');
    }

    public function deactivateAccount(Request $request)
    {
        $username = session('username');
        if (!$username) {
            return redirect('/login')->with('error', 'Please login first');
        }

        $user = User::where('username', $username)->first();
        if (!$user || ($user->role !== 'pet_owner' && $user->role !== 'registered_user')) {
            return redirect('/login')->with('error', 'Access denied');
        }

        $user->update(['is_active' => 0]);

        Auth::logout();
        $request->session()->forget('username');
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->with('success', 'Your account has been deactivated.');
    }
}
