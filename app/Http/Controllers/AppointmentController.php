<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Doctor;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Illuminate\Support\Facades\Redirect;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class AppointmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $doctors=Doctor::with('appointments')->paginate(10);
        return view('backend_app.appointments.index',compact('doctors'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        $doctor = Doctor::findOrFail($request->doctor_id);
        $appointmentDate = $request->input('appointment_date');
        $dateTime = Carbon::parse($appointmentDate);
        
        // Separate date and time
        $date = $dateTime->toDateString(); // Y-m-d format as a date type, e.g., "2024-10-30"
        $time = $dateTime->toTimeString(); 
        // Set Stripe API Key
        Stripe::setApiKey(env('STRIPE_SECRET'));
    
        // Define the checkout session
        $checkoutSession = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => [
                        'name' => "Appointment with Dr. {$doctor->name}",
                    ],
                    'unit_amount' => $doctor->price * 100, // Price in cents
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => route('appointment.success', [
                'doctor_id' => $doctor->id,
                'user_id' => $user->id,
                'appointment_date' => $date,
                'start_time'=>$time
            ]),
            'cancel_url' => route('appointment.cancel'),
        ]);
    
        // Redirect to Stripe Checkout page
        return Redirect::away($checkoutSession->url);
    }


    public function appointmentSuccess(Request $request){
       try {
        Appointment::create([
            'user_id'=>$request->user_id,
            'doctor_id'=>$request->doctor_id,
            'date'=>$request->appointment_date,
            'start_time'=>$request->start_time,
        ]);
        $doctor=Doctor::find($request->doctor_id);
        createNotification($request->user_id,"Payment Success", 'Appointment has been booked successfully with Dr '.$doctor->name);
        return view('backend_app.payment_status.success');
       } catch (\Throwable $th) {
        return $th->getMessage();
       }
    }

    public function appointmentCancel(){
        try {
           return view('backend_app.payment_status.cancelled');
        } catch (\Throwable $th) {
            return $th->getMessage();
        }
    }
    /**
     * Display the specified resource.
     */
    public function show()
    {
        try {
            $user=Auth::user();
            if($user->role === "admin"){
            $data=Appointment::with('user')->paginate(15);
        }   
        else{
        $data=Appointment::with('user','doctor')->where('user_id',$user->id)->paginate(15);
        }

        return view('backend_app.appointments.show',compact('data'));

        } catch (\Throwable $th) {
            return back()->with("error",$th->getMessage());
        }
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
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
