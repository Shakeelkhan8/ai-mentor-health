<?php

namespace App\Http\Controllers;

use App\Mail\NewsLetterMail;
use Illuminate\Http\Request;
use App\Models\ContactForm;
use Mail;


class MainController extends Controller
{
    public function index()
    {

        return view('backend_app.index');
    }

    public function send_Mail(Request $request)
    {

        Mail::to($request->email)->send(new NewsLetterMail($request->email));
        return back()->with("success", "Mail Has been sent successfully");
    }

    public function searchhospitals(Request $request)
    {

        $latitude = $request->latitude;
        $longitude = $request->longtitude;
        $client = new \GuzzleHttp\Client();

        $response = $client->request('GET', 'https://maps-data.p.rapidapi.com/nearby.php', [
            'query' => [
                'query' => 'hospitals',
                'lat' => $latitude,
                'lng' => $longitude,
                'limit' => 7,
                'country' => 'pak',
                'lang' => 'en',
                'offset' => 0,
                'zoom' => 12
            ],
            'headers' => [
                'x-rapidapi-host' => 'maps-data.p.rapidapi.com',
                'x-rapidapi-key' => env('RAPIDAPI_KEY'),
            ],
        ]);

        $data = json_decode($response->getBody()->getContents(), true);

        if (isset($data['data'])) {
            $hospitals = $data['data'];
        } else {
            $hospitals = [];
        }

        return view('backend_app.hospitals.index', compact('hospitals'));
    }

    public function formSubmit(Request $request)
    {
        $validated = $request->validate([
            'name'   => 'required|string|max:255',
            'phone'   => 'required|string|max:20',
            'email'   => 'required|email|max:255',
            'message' => 'required|string',
        ]);
        try {

            ContactForm::create($validated);
            return back()->with('success', 'Thank you for contacting us. We will be in touch soon!');
            //code...
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
    }
    public function forms()
    {
        $forms = ContactForm::all();
        return view('backend_app.community-form.index', compact('forms'));
    }
}
