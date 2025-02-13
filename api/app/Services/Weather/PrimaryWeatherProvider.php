<?php

namespace App\Services\Weather;

use App\Contracts\WeatherProviderInterface;
use Illuminate\Support\Facades\Http;

class PrimaryWeatherProvider implements WeatherProviderInterface
{
    /**
     * Fetch weather data for the given location.
     *
     * @param array $location An associative array with keys 'latitude' and 'longitude'
     * @return array An array containing the weather information
     *
     * @throws \Exception if the weather data cannot be retrieved
     */
    public function fetchWeather(array $location): array
    {
        // -- Fetch weather data from the primary weather provider

        $url = config('weather.primary.endpoint');
        $key = config('weather.primary.api_key');
        $lat = $location['latitude'];
        $lng = $location['longitude'];

        $weather_response = Http::retry(3)->get("$url?lat=$lat&lon=$lng&appid=$key&units=metric");

        if ($weather_response->getStatusCode() !== 200) {
            return [];
        }

        $response = [
            'city'                  => $weather_response['name'],
            // 'country'               => $weather_response['sys']['country'],
            'latitude'              => $weather_response['coord']['lat'],
            'longitude'             => $weather_response['coord']['lon'],
            'observation_time'      => gmdate('c', $weather_response['dt']),
            'temperature'           => $weather_response['main']['temp'],
            'weather_description'   => $weather_response['weather'][0]['description'],
            'weather_icon'          => $weather_response['weather'][0]['icon'],
            'provider'              => 'primary',
        ];

        return  $response;
    }
}
