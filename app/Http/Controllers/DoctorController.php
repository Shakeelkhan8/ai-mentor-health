<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\DoctorFeedback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DoctorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = Doctor::all();
        return view('backend_app.doctors.index', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('backend_app.doctors.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required',
                'email' => 'required',
                'phone_no' => 'required',
                'designation' => 'required',
                'country' => 'required',
                'city' => 'required',
                'address' => 'required',
                'price' => 'required'
            ]);
            $img = null;
            if ($request->hasFile('img')) {
                $image = $request->file('img');
                $imagename = $request->file('img')->getClientOriginalName();
                $destinationpath = public_path('assets/doctors/');
                $image->move($destinationpath, $imagename);
                $img = $imagename;
            }
            Doctor::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone_no' => $request->phone_no,
                'designation' => $request->designation,
                'country' => $request->country,
                'city' => $request->city,
                'address' => $request->address,
                'price' => $request->price,
                'img' => $img
            ]);

            return back()->with('success', "Doctor has been added successfully");
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $user = Doctor::find($id);
        return view('backend_app.doctors.detail', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {

            $request->validate([
                'name' => 'required',
                'email' => 'required',
                'phone_no' => 'required',
                'designation' => 'required',
                'country' => 'required',
                'city' => 'required',
                'address' => 'required',
            ]);
            $data = Doctor::find($id);
            $data->update([
                'name' => $request->name,
                'email' => $request->email,
                'phone_no' => $request->phone_no,
                'designation' => $request->designation,
                'country' => $request->country,
                'city' => $request->city,
                'address' => $request->address,
            ]);

            return back()->with('success', "Doctor has been updated successfully");
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        Doctor::destroy($id);

        return back()->with('success', 'Doctor has been deleted successfully');
    }

    public function addFeedback(Request $request)
    {
        try {
            $user = Auth::user();
            DoctorFeedback::create([
                'user_id' => $user->id,
                'doctor_id' => $request->doctor_id,
                'message' => $request->message,
                'stars' => $request->stars
            ]);
            return back()->with('success', "Feedback has been added successfully");
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
    }
}
