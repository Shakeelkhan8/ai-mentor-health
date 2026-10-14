<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use App\Models\User;
use App\Support\ImageUpload;
use Illuminate\Support\Facades\Hash;
class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
       $user=Auth::user();
       $data=User::find($user->id);
       if ($request->hasFile('img')){
        $data->img=ImageUpload::store($request->file('img'), 'avatars');
       }

       $data->name=$request->name;
       $data->email=$request->email;
       // A new address has not been verified yet.
       if ($data->isDirty('email')) {
           $data->email_verified_at = null;
       }
       $data->organization=$request->organization;
       $data->phone_no=$request->phone_no;
       $data->address=$request->address;
       $data->country=$request->country;
       $data->save();

       return back()->with('success','User has been updated successfully');

    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
    public function edit_profile(){
        $user=Auth::user();
        return view('backend_app.edit_profile',compact('user'));
    }
    public function update_password(Request $request){
        // Validate the request data

        $request->validate([
            'currentPassword' => 'required',
            'newPassword' => 'required|min:8|confirmed',
        ]);

        // Get the current user
        $user = Auth::user();

        // Verify if the current password matches the user's password
        if (!Hash::check($request->currentPassword, $user->password)) {
            return back()->with('error' , 'Current passsowrd : The current password is incorrect.');
        }

        // Update the user's password
        $user->password = Hash::make($request->newPassword);
        $user->save();

        // Redirect with success message
        return redirect()->route('edit_profile')->with('success', 'Password updated successfully.');
    }
}
