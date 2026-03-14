<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Hash;
use Throwable;

class UserController extends Controller
{
    public function register(Request $request){

        $getData = $request->validate([
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'address' => 'required|string|max:100',
            'contact_number' => 'required|string|max:20',
            'email' => 'required|string|email|max:255|unique:users,email',
            'username' => 'required|string|max:50|unique:users,username',
            'password' => 'required|string|min:6|confirmed'
        ],
        [
            'password.confirmed' => 'Password does not match'
        ]);

             $getData['password'] = bcrypt($getData['password']);
             $user = User::create($getData);

             if ($user->shouldBypassEmailVerification()) {
                 if (! $user->email_verified) {
                     $user->forceFill(['email_verified' => true])->save();
                 }

                 return redirect('/login')->with('success', 'Demo account registered successfully. Email verification was skipped for testing.');
             }

             $request->session()->put('verification_email', $user->email);

             $flash = [
                 'verification_email' => $user->email,
             ];

             try {
                 event(new Registered($user));
                 $flash['success'] = 'Registration successful. Please verify your email to continue.';
             } catch (Throwable $e) {
                 report($e);
                 $errorText = $e->getMessage();
                 $isAuthFailure = str_contains($errorText, 'BadCredentials') || str_contains($errorText, 'Failed to authenticate on SMTP server');
                 $flash['warning'] = $isAuthFailure
                     ? 'Registration successful, but Gmail rejected SMTP login. Regenerate Google App Password for MAIL_USERNAME and try resend.'
                     : 'Registration successful, but we could not send the verification email yet. Please update mail settings and use resend.';
                 $flash['verification_link'] = \Illuminate\Support\Facades\URL::temporarySignedRoute(
                     'verification.verify',
                     now()->addMinutes(60),
                     [
                         'id' => $user->id,
                         'hash' => sha1($user->getEmailForVerification()),
                     ]
                 );
             }

             return redirect()->route('verification.notice')->with($flash);
    }

    public function login(Request $request)
{
    $credentials = $request->validate([
        'username' => 'required|string',
        'password' => 'required|string'
    ]);

    $username = (string) $credentials['username'];
    $user = User::where('username', $username)->first();

    if ($user && Hash::check($credentials['password'], $user->password)) {
        if (! $user->is_active) {
            return back()->withErrors([
                'username' => 'Your account is deactivated. Please contact the clinic administrator.',
            ])->withInput(['username' => $username]);
        }

        // Admin accounts can log in without email verification.
        $requiresEmailVerification = $user->role !== 'admin';

        if ($requiresEmailVerification && ! $user->hasVerifiedEmail()) {
            try {
                $user->sendEmailVerificationNotification();
                $message = 'Please verify your email before logging in. A new verification link has been sent.';
            } catch (Throwable $e) {
                report($e);
                $errorText = $e->getMessage();
                $isAuthFailure = str_contains($errorText, 'BadCredentials') || str_contains($errorText, 'Failed to authenticate on SMTP server');
                $message = $isAuthFailure
                    ? 'Please verify your email before logging in. Gmail SMTP authentication failed. Regenerate app password and try resend.'
                    : 'Please verify your email before logging in. We could not send the verification email right now. Check mail settings and try resend.';

                session([
                    'verification_email' => $user->email,
                    'verification_link' => \Illuminate\Support\Facades\URL::temporarySignedRoute(
                        'verification.verify',
                        now()->addMinutes(60),
                        [
                            'id' => $user->id,
                            'hash' => sha1($user->getEmailForVerification()),
                        ]
                    ),
                ]);
            }

            return back()->withErrors([
                'username' => $message,
            ])->withInput(['username' => $username]);
        }

        // Log the user in using Laravel's Auth
        \Illuminate\Support\Facades\Auth::login($user);
        
        // Also store username in session for backward compatibility
        session(['username' => $username]);
        
        // Redirect based on user role
        $adminRoles = ['admin', 'veterinarian', 'pharmacy', 'reception', 'boarding', 'groomer', 'staff'];
        if (in_array($user->role, $adminRoles)) {
            return redirect('/admin/dashboard');
        } elseif($user->role == 'pet_owner' || $user->role == 'registered_user') {
            return redirect('/customer/dashboard');
        } else{
            return redirect('/dashboard');
        }
   
    }

    return back()->withErrors([
        'username' => 'Invalid username or password'
    ])->withInput(['username' => $username]);
}
}