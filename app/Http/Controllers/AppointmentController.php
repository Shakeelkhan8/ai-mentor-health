<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Doctor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Stripe\Checkout\Session;
use Stripe\Exception\ApiErrorException;
use Stripe\Stripe;
use Carbon\Carbon;
use Illuminate\Support\Facades\Redirect;

class AppointmentController extends Controller
{
    public function index()
    {
        $doctors = Doctor::with('appointments')->paginate(10);

        return view('backend_app.appointments.index', compact('doctors'));
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'doctor_id' => ['required', 'integer', 'exists:doctors,id'],
            'appointment_date' => ['required', 'date'],
        ]);

        $user = $request->user();
        $doctor = Doctor::query()->findOrFail($validated['doctor_id']);
        $dateTime = Carbon::parse($validated['appointment_date']);
        $date = $dateTime->toDateString();
        $time = $dateTime->toTimeString();

        $secret = config('services.stripe.secret');
        if (blank($secret)) {
            return back()->withErrors(['billing' => 'Stripe is not configured.']);
        }

        Stripe::setApiKey($secret);

        $checkoutSession = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => [
                        'name' => "Appointment with Dr. {$doctor->name}",
                    ],
                    'unit_amount' => (int) round(((float) $doctor->price) * 100),
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'client_reference_id' => (string) $user->id,
            'metadata' => [
                'user_id' => (string) $user->id,
                'doctor_id' => (string) $doctor->id,
                'appointment_date' => $date,
                'start_time' => $time,
            ],
            'success_url' => route('appointment.success').'?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('appointment.cancel'),
        ]);

        return Redirect::away($checkoutSession->url);
    }

    /**
     * Confirm payment via Stripe Checkout Session only.
     * Never trusts client-supplied user/doctor/date query params.
     */
    public function appointmentSuccess(Request $request)
    {
        $validated = $request->validate([
            'session_id' => ['required', 'string'],
        ]);

        $user = $request->user();
        abort_unless($user !== null, 403);

        $secret = config('services.stripe.secret');
        if (blank($secret)) {
            abort(503, 'Stripe is not configured.');
        }

        Stripe::setApiKey($secret);

        try {
            $session = Session::retrieve($validated['session_id']);
        } catch (ApiErrorException $e) {
            Log::warning('Invalid Stripe checkout session on appointment success', [
                'session_id' => $validated['session_id'],
                'error' => $e->getMessage(),
            ]);
            abort(400, 'Invalid checkout session.');
        }

        if (($session->mode ?? null) !== 'payment') {
            abort(400, 'Invalid checkout mode.');
        }

        if (($session->payment_status ?? null) !== 'paid') {
            abort(402, 'Payment not completed.');
        }

        $metaUserId = (int) ($session->metadata->user_id ?? $session->client_reference_id ?? 0);
        abort_unless($metaUserId === (int) $user->id, 403);

        $doctorId = (int) ($session->metadata->doctor_id ?? 0);
        $date = $session->metadata->appointment_date ?? null;
        $time = $session->metadata->start_time ?? null;

        if ($doctorId < 1 || blank($date) || blank($time)) {
            abort(422, 'Checkout session is missing appointment metadata.');
        }

        $doctor = Doctor::query()->findOrFail($doctorId);

        $appointment = Appointment::query()->firstOrCreate(
            [
                'user_id' => $user->id,
                'doctor_id' => $doctor->id,
                'date' => $date,
                'start_time' => $time,
            ]
        );

        if ($appointment->wasRecentlyCreated) {
            createNotification(
                $user->id,
                'Payment Success',
                'Appointment has been booked successfully with Dr '.$doctor->name
            );
        }

        return view('backend_app.payment_status.success');
    }

    public function appointmentCancel()
    {
        return view('backend_app.payment_status.cancelled');
    }

    public function show()
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            $data = Appointment::with('user')->paginate(15);
        } else {
            $data = Appointment::with('user', 'doctor')
                ->where('user_id', $user->id)
                ->paginate(15);
        }

        return view('backend_app.appointments.show', compact('data'));
    }

    public function edit(string $id)
    {
        //
    }

    public function update(Request $request, string $id)
    {
        //
    }

    public function destroy(string $id)
    {
        //
    }
}
