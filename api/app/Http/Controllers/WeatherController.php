<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Weather\WeatherService;

class WeatherController extends Controller
{
    protected WeatherService $weatherService;

    public function __construct(WeatherService $weatherService)
    {
        $this->weatherService = $weatherService;
    }

    /**
     * Return weather data for a given location.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($lat, $lng)
    {
        $validator = validator([
            'lat' => $lat,
            'lng' => $lng
        ], [
            'lat' => ['required', 'string', 'max:255'],
            'lng' => ['required', 'string', 'max:255']
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Latitude and longitude are required. ' . $validator->errors()], 400);
        }

        // -- Start
        try {
            // -- Fetch weather data for the given location using the WeatherService
            $weatherData = $this->weatherService->getWeather([
                'latitude' => $lat,
                'longitude' => $lng,
            ]);

            return response()->json($weatherData, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
