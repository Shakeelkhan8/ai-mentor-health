<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Client;
use App\Models\Distributor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use App\Models\User;
use App\Models\File;
use App\Models\Ledger;
use Illuminate\Support\Facades\Hash;
class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request,$id): View
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
        $image=$request->file('img');
        $imagename=$request->file('img')->getClientOriginalName();
        $destinationpath=public_path('assets/users/');
        $image->move($destinationpath,$imagename);
        $data->img=$imagename;
       }

       $data->name=$request->name;
       $data->email=$request->email;
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
    public function distributor_profile($id){
        try {
        $data=Distributor::with('client')->find($id);
        $files=File::where('distributor_id',$id)->get();
        $total_amount=File::where('distributor_id',$id)->sum('total_amount');
        $processing=File::where('distributor_id',$id)->where("file_status","processing")->get()->count();
        $blocked=File::where('distributor_id',$id)->where("file_status","blocked")->get()->count();
        $close=File::where('distributor_id',$id)->where("file_status","close")->get()->count();
        $ready=File::where('distributor_id',$id)->where("file_status","ready")->get()->count();
        $open=File::where('distributor_id',$id)->where("file_status","open")->get()->count();
        $reserved=File::where('distributor_id',$id)->where("file_status","reserved")->get()->count();
        $paid_amount=0;
        $ledgers=[];
        foreach ($files as $item) {
            $ledger = Ledger::where('file_id', $item->id)->first();

            // Check if a ledger entry was found before adding it to the array
            if ($ledger) {
                $ledgers[] = $ledger;
            }
        }
        foreach($ledgers as $item){

            $paid_amount+=$item->paid_amount;
        }


        return view('backend_app.user_profile',compact('data','total_amount','paid_amount','files','processing','blocked','close','ready','open','reserved'));

        } catch (\Throwable $th) {

        return back()->with('error',$th->getMessage());

        }
    }
    public function client_profile($id){
        $data=Client::with('distributor')->find($id);
        $filesData = json_decode($data->files);
        $files=[];
        $total_amount=0;
        foreach($filesData as $key=>$item){
            $file=File::where('id',$item)->first();
            $total_amount+=File::where('id',$item)->sum('total_amount');
        }
        if($file){
            $files[]=$file;
        }
        $paid_amount=0;
        foreach($files as $item){
            $paid_amount=Ledger::where('file_id',$item->id)->sum('paid_amount');
        }

        return view('backend_app.client.client_profile',compact('data','total_amount','paid_amount'));
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
