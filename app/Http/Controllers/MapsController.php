<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;

class MapsController extends Controller
{
    public function getDirections(Request $request)
{
    try {
        $validated = $request->validate([
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:100',
            'places_list' => 'required|string',
            'address' => 'nullable|string',
        ]);

        $places = json_decode($request->input('places_list'), true);

        if (count($places) < 1) {
            return response()->json(['error' => 'Please provide at least one place.'], 400);
        }

        // Set the starting address to the provided address or default to city and state
        $origin = $validated['address'] ?? $validated['city'] . ', ' . $validated['state'];

        $apiKey = env('GOOGLE_MAPS_API_KEY');
        $client = new Client();
        $responseList = [];

        // Generate directions from starting address to each place
        foreach ($places as $destination) {
            $url = "https://maps.googleapis.com/maps/api/directions/json?origin=" . urlencode($origin) . "&destination=" . urlencode($destination) . "&key=" . $apiKey;

            try {
                $response = $client->get($url);
                $data = json_decode($response->getBody(), true);

                if (isset($data['routes']) && count($data['routes']) > 0) {
                    $googleMapsLink = "https://www.google.com/maps/dir/?api=1&origin=" . urlencode($origin) . "&destination=" . urlencode($destination);
                    $embedLink = "https://www.google.com/maps/embed/v1/directions?key=" . $apiKey . "&origin=" . urlencode($origin) . "&destination=" . urlencode($destination);

                    $responseList[] = [
                        'origin' => $origin,
                        'destination' => $destination,
                        'directions' => $this->formatDirections($data['routes']),
                        'googleMapsLink' => $googleMapsLink,
                        'embedLink' => $embedLink
                    ];
                }
            } catch (RequestException $e) {
                Log::error('Error with Google Maps API request:', [
                    'url' => $url,
                    'error' => $e->getMessage(),
                    'response' => $e->hasResponse() ? (string) $e->getResponse()->getBody() : 'No response'
                ]);
                return response()->json(['error' => 'Error with Google Maps API request.'], 500);
            }
        }

        return response()->json($responseList);
    } catch (\Exception $e) {
        Log::error('Error getting google maps directions:', [
            'exception' => $e,
            'request' => $request->all()
        ]);

        return response()->json(['error' => 'An error occurred while getting google maps directions.'], 500);
    }
}


    private function formatDirections($routes)
    {
        $formattedDirections = [];

        foreach ($routes as $route) {
            $legs = $route['legs'];
            foreach ($legs as $leg) {
                $steps = $leg['steps'];
                foreach ($steps as $step) {
                    $formattedDirections[] = [
                        'distance' => $step['distance']['text'],
                        'duration' => $step['duration']['text'],
                        'instructions' => strip_tags($step['html_instructions']),
                        'start_location' => $step['start_location'],
                        'end_location' => $step['end_location'],
                    ];
                }
            }
        }

        return $formattedDirections;
    }
}
