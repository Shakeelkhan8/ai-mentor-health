<?php

namespace App\Http\Controllers;

use App\Mail\NewsLetterMail;
use App\Models\DiscountForm;
use App\Models\Form;
use Illuminate\Http\Request;
use App\Models\File;
use App\Models\Client;
use App\Models\ContactForm;
use App\Models\Distributor;
use App\Models\Ledger;
use Mail;


class MainController extends Controller
{
    public function index()
    {

        return view('backend_app.index');
    }

    public function delete_all(Request $request)
    {

        if ($request->datafrom === "files") {
            foreach ($request->items as $key => $value) {
                Ledger::where('file_id', $value)->delete();
                File::destroy($value);
            }
        } elseif ($request->datafrom === "dealers") {

            foreach ($request->items as $key => $value) {
                File::where('distributor_id', $value)->update(['distributor_id' => null]);
                Distributor::destroy($value);
            }
        } elseif ($request->datafrom === "clients") {

            foreach ($request->items as $key => $value) {
                Client::destroy($value);
            }
        } elseif ($request->datafrom === "discounted") {

            foreach ($request->items as $key => $value) {
                DiscountForm::destroy($value);
            }
        } elseif ($request->datafrom === "customer") {

            foreach ($request->items as $key => $value) {
                Form::destroy($value);
            }
        }

        $response = [
            "success" => true,
            "message" => "Your Files has been deleted successfully"
        ];
        return response()->json($response);
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
                'x-rapidapi-key' => '0e1831b781msh66cc05a2d58a7e2p1b6114jsn017dec11439c',
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
