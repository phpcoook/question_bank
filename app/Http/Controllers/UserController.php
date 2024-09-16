<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Models\User;


class UserController extends Controller
{
    public function loginView()
    {
        return view('adminlogin');
    }

    public function login(Request $request)
    {
        try {

            // Validate the login form data
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required|min:6',
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            // Check if the user exists and email is verified
            $user = User::where('email', $request->input('email'))->first();

            if ($user && $user->email_verified_at !== null) {
                // Attempt to authenticate the user
                if (Auth::attempt(['email' => $request->input('email'), 'password' => $request->input('password')])) {
                    if (Auth::user()->role == 'admin') {
                        return redirect()->route('question.index'); // Redirect to intended page or dashboard
                    }else if(Auth::user()->role == 'student' || Auth::user()->role == 'tutor'){
                        return redirect()->route('login');
                    }
                } else {
                    return redirect()->back()
                        ->withErrors(['password' => 'Invalid credentials'])
                        ->withInput();
                }
            } else {
                return redirect()->back()
                    ->withErrors(['email' => 'Email not verified or does not exist'])
                    ->withInput();
            }
        } catch (\Exception $e) {
            Log::info('In File : ' . $e->getFile() . ' - Line : ' . $e->getLine() . ' - Message : ' . $e->getMessage() . ' - At Time : ' . date('Y-m-d H:i:s'));
            return redirect()->back()->with('error', 'An error occurred. Please try again.');
        }
    }

    public function logout(Request $request)
    {
        if (Auth::user()->role == 'admin') {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('admin.login')->with('status', 'Successfully logged out.');
        }
        Auth::logout();
        return redirect()->route('login')->with('status', 'Successfully logged out.');
    }
}
