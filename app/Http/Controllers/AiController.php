<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use OpenAI\Laravel\Facades\OpenAI;
use GuzzleHttp\Client;

class AiController extends Controller
{
    public function index()
    {
        return  view('backend_app.chatbot.index');
    }
    public function get_response(Request $request)
    {
        $client = new \GuzzleHttp\Client();
        $searchQuery = trim($request->search); // Ensure no leading/trailing spaces
        $conversationHistory = $request->conversation; // Get conversation history

        // Adjust the prompt to encourage a more human-like response
        $prompt = "You are a friendly and supportive health professional, acting like a psychologist and a friend. Your role is to actively listen to the person's concerns and provide thoughtful, polite, and empathetic responses. Engage in a friendly conversation, making it feel natural and supportive.response in 3/2 lines. Here is the conversation history:\n{$conversationHistory}\nUser: {$searchQuery}\nAI:";

        try {
            $response = $client->post('https://meta-llama-3-1-405b.p.rapidapi.com/', [
                'headers' => [
                    'x-rapidapi-key' => env('RAPIDAPI_KEY'),
                    'x-rapidapi-host' => 'meta-llama-3-1-405b.p.rapidapi.com',
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'model' => 'llama-3.1-405b',
                    'messages' => [
                        [
                            'role' => 'user',
                            'content' => $prompt,
                        ]
                    ],
                ]
            ]);

            // Return the AI's response as JSON
            return response()->json(json_decode($response->getBody()), 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to get a response from the AI.'], 500);
        }
    }
}
