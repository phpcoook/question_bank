<?php

namespace App\Http\Controllers;

use App\Jobs\ForgotPassword;
use App\Mail\Register;
use App\Models\Subscription;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use App\Models\User;


class UserController extends Controller
{
    public function login()
    {
        if (Auth::check()) {
            $role = Auth::user()->role;
            if ($role == 'student') {
                return redirect()->route('student.dashboard');
            } elseif ($role == 'tutor') {
                return redirect()->route('tutor.dashboard');
            } elseif ($role == 'admin') {
                return redirect()->route('question.index');
            }
        }
        return view('login');
    }

    public function loginProcess(Request $request)
    {
        try {

            // Validate the login form data
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required|min:8',
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            $user = User::where('email', $request->input('email'))->first();

            if ($user && $user->email_verified_at !== null) {
                if (Auth::attempt(['email' => $request->input('email'), 'password' => $request->input('password')])) {
                    $role = Auth::user()->role;
                    if ($role == 'student') {
                        return redirect()->route('student.dashboard');
                    } elseif ($role == 'tutor') {
                        return redirect()->route('tutor.dashboard');
                    } elseif ($role == 'admin') {
                        return redirect()->route('question.index');
                    } else {
                        return redirect()->back()
                            ->withErrors(['password' => 'Unauthorized Request!'])
                            ->withInput();
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
        Auth::logout();
        return redirect('/')->with('status', 'Successfully logged out.');
    }

    public function forgotPassword()
    {
        return view('forgot-password');
    }

    public function resetPassword(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
            ]);
            if ($validator->fails()) {
                return back()->withInput()->withErrors($validator);

            } else {
                $user = User::where('email', $request->email);
                if ($user->count()) {
                    $arrayEmails = $request->email;
                    $emailSubject = "Please Reset Your Password";
                    Mail::send('forgot',
                        ['userDetails' => $user->first()],
                        function ($message) use ($arrayEmails, $emailSubject) {
                            $message->to($arrayEmails)
                                ->subject($emailSubject);
                        }
                    );
                    return redirect('/')->with('success', 'Email sent successfully');
                } else {
                    return redirect("forgot-password")->with('error', 'Email is not Registered!');
                }
            }
        } catch (\Exception $e) {
            Log::info('In File : ' . $e->getFile() . ' - Line : ' . $e->getLine() . ' - Message : ' . $e->getMessage() . ' - At Time : ' . date('Y-m-d H:i:s'));

        }
    }

    public function resetPasswordProcess($id)
    {
        try {
            $userId = Crypt::decryptString($id);
            return view('reset-password', compact('userId'));
        } catch (\Exception $e) {
            Log::info('In File : ' . $e->getFile() . ' - Line : ' . $e->getLine() . ' - Message : ' . $e->getMessage() . ' - At Time : ' . date('Y-m-d H:i:s'));
            return redirect('error/' . $e->getCode())->with([
                'message' => 'Something Went Wrong!',
                'class' => 'danger'
            ]);
        }
    }

    public function changePassword(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'password' => 'required|max:16',
                'confirm_password' => 'required|same:password|max:16',
            ]);
            if ($validator->fails()) {
                return redirect()->back()->withInput()->withErrors($validator->errors());
            }
            $user = User::findOrFail($request->user_id);
            $user->password = Hash::make($request->password);
            $user->save();
            return redirect('/')->with('success', 'Password has been changed! Login Now!');
        } catch (\Exception $e) {
            Log::info('In File : ' . $e->getFile() . ' - Line : ' . $e->getLine() . ' - Message : ' . $e->getMessage() . ' - At Time : ' . date('Y-m-d H:i:s'));
            return redirect('error/' . $e->getCode())->with([
                'success' => 'Something Went Wrong!',
                'class' => 'danger'
            ]);
        }
    }

    public function profile(){
        $subscription = null;
        if(Auth::user()->role == 'student'){
            $subscription = Subscription::where('user_id', Auth::user()->id)->whereDate('end_date', '>', now())->first();
            if(!empty($subscription) && $subscription->status == 'active') {
                $timestamp = strtotime($subscription->end_date);
                $date = new DateTime();
                $date->setTimestamp($timestamp);
                $date->modify('+1 day');
                $subscription['startDate'] = date('jS M - Y h:i A', strtotime($subscription->start_date));
                $subscription['endDate'] = date('jS M - Y h:i A', strtotime($subscription->end_date));
                $subscription['renewalDate'] = $date->format('jS M - Y h:i A');
            }
        }
        return view('profile',compact('subscription'));
    }

    public function updatePassword(Request $request){
        try{
            $user = User::findOrFail(Auth::user()->id);
            $validator = Validator::make($request->all(), [
                'old_password' => 'required|max:16',
                'new_password' => 'required|different:old_password|max:16',
                'confirm_password' => 'required|same:new_password|max:16',
            ]);
            if ($validator->fails()) {
                return redirect()->back()->withInput()->withErrors($validator->errors());
            }
            if (Hash::check($request->old_password, $user->password)) {
                $user->password = Hash::make($request->new_password);
                $user->save();
                return redirect()->back()->with(['success'=> 'Password has been changed!','class'=>'success']);
            } else {
                return redirect()->back()->with(['error'=>'Old Password does not match','class'=>'danger']);
            }
        } catch (Exception $e) {
            Log::info('In File : '.$e->getFile().' - Line : '.$e->getLine().' - Message : '.$e->getMessage().' - At Time : '.date('Y-m-d H:i:s'));
            return redirect('error/'.$e->getCode())->with(['success'=> 'Something Went Wrong!','class'=>'danger']);
        }
    }

    public function updateProfile(Request $request){
        try{
            $id = Auth::user()->id;
            $validator = Validator::make($request->all(), [
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'date_of_birth' => 'required|date'
            ]);
            if ($validator->fails()) {
                return back()->withInput()->withErrors($validator);
            } else {
                $student = User::findOrFail($id);
                $student->first_name = $request->first_name;
                $student->last_name = $request->last_name;
                $student->date_of_birth = $request->date_of_birth;
                $student->save();
                return redirect()->back()->with('success', 'Student Update successfully.');
            }

        } catch (Exception $e) {
            Log::info('In File : '.$e->getFile().' - Line : '.$e->getLine().' - Message : '.$e->getMessage().' - At Time : '.date('Y-m-d H:i:s'));
            return redirect('error/'.$e->getCode())->with(['success'=> 'Something Went Wrong!','class'=>'danger']);
        }
    }

    public function updateAdminProfile(Request $request){
        try{
            $id = Auth::user()->id;
            $validator = Validator::make($request->all(), [
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $id,
            ]);
            if ($validator->fails()) {
                return back()->withInput()->withErrors($validator);
            } else {
                $admin = User::findOrFail($id);
                $admin->first_name = $request->first_name;
                $admin->last_name = $request->last_name;
                $admin->email = $request->email;
                $admin->save();
                return redirect()->back()->with('success', 'Profile Update successfully.');
            }

        } catch (Exception $e) {
            Log::info('In File : '.$e->getFile().' - Line : '.$e->getLine().' - Message : '.$e->getMessage().' - At Time : '.date('Y-m-d H:i:s'));
            return redirect('error/'.$e->getCode())->with(['success'=> 'Something Went Wrong!','class'=>'danger']);
        }
    }
}
