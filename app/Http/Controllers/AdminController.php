<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\AgentController;
use App\Http\Requests\Teacher;

class AdminController extends Controller
{
    public function AdminDashboard()
    {

        // return view('show', compact('data'));
        return view('admin.body.content.index');
    }

    // public function index()
    // {
    //     $data = Form::all();

    //     return view('show', compact('data'));
    // }


    public function AdminLogout(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/login');
    }
    public function AdminLogin()
    {
        return view('admin.admin_login');
    }

    public function AdminProfile()
    {
        $id = Auth::user()->id;
        $profileData = User::find($id);
        return view('admin.admin_profile_view', compact('profileData'));
    }

    public function AdminProfileStore(Request $request)
    {
        $id = Auth::user()->id;
        $data = User::find($id);
        $data->username = $request->username;
        $data->name = $request->name;
        $data->email = $request->email;
        $data->phone = $request->phone;
        $data->address = $request->address;

        if ($request->file('photo')) {
            $file = $request->file('photo');
            @unlink(public_path('upload/admin_image/' . $data->photo));
            $filename = date('YmdHi') . $file->getClientOriginalName();
            $file->move(public_path('upload/admin_image'), $filename);
            $data['photo'] = $filename;
        }
        $data->save();
        $notification = array(
            'message' => 'Admin Profile Updated Sucessfully',
            'alert-type' => 'success'
        );
        return redirect()->back()->with($notification);
    }

    public function AdminChangePassword()
    {
        $id = Auth::user()->id;
        $profileData = User::find($id);

        return view('admin.admin_change_password', compact('profileData'));
    }

    public function TeacherUpdate(Request $request)
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        // return route('');
    }

    public function TeacherTable()
    {
        $data = User::all();
        return view('admin.body.content.teacher', compact('data'));
    }

    public function update(Teacher $request, User $form)
    {
        // dd($request['email']);

        // Get the updated name from the form input
        if ($request->input('name')) {
            $updated = $request->input('name');
            $form->name = $updated;
            // dd($form->name);
            // print_r($updated);
        } elseif ($request->input('contact')) {
            $updated = $request->input('contact');
            $form->phone = $updated;
            // print_r($_POST);
            // dd($form->contact);

        } elseif ($request->input('email')) {
            // dd($form->email);
            $updated = $request->input('email');
            $form->email = $updated;
        } elseif ($request->input('address')) {
            $updated = $request->input('address');
            $form->address = $updated;
            // dd($form->email);

        } elseif ($request->input('password')) {
            $updated = bcrypt($request->input('password'));
            $form->password = $updated;
            // dd($form->email);

        } elseif ($request->input('status')) {
            $updated = $request->input('status');
            $form->role = $updated;
        }

        // Update the name in the model
        // dd($form->role);
        // dd($request->input('role'));
        // dd($_POST);S

        $form->save();

        return redirect()->back()->with('succcess', 'Updated successfully');
    }


    public function AdminUpdatePassword(Request $request)
    {
        $request->validate([
            'old_password' => 'required',
            'new_password' => 'required|confirmed'
        ]);

        if (!Hash::check($request->old_password, auth::user()->password)) {
            $notification = array(
                'message' => 'Old Password Does not match',
                'alert-type' => 'error'
            );
            return back()->with($notification);
        }
        User::whereId(auth()->user()->id)->update([
            'password' => Hash::make($request->new_password)
        ]);

        $notification = array(
            'message' => 'Password Changed Sucessfully',
            'alert-type' => 'success'
        );
        return back()->with($notification);
    }

    public function delete(User $form)
    {
        $form->delete();
        return redirect()->back()->with('succcess', 'Form deleted successfully');
    }
    // public function dashboard()
    // {
    //     $user = Auth::user();

    //     if ($user['role'] == 'admin') {
    //         // Redirect to AdminController's AdminDashboard method
    //         return redirect()->action([AdminController::class, 'AdminDashboard']);
    //     } elseif ($user['role'] == 'teacher') {
    //         // Redirect to AgentController's AgentDashboard method
    //         return redirect()->action([AgentController::class, 'AgentDashboard']);
    //     } elseif ($user['role'] == 'user') {
    //         // Redirect to AgentController's userDashboard method
    //         return redirect()->action([AgentController::class, 'userDashboard']);
    //     }
    // }

    public function class(){
       return view('admin.content.class');
    }
}
