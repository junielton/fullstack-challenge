<?php

namespace App\Services\Weather;

use App\Contracts\WeatherProviderInterface;
use Illuminate\Support\Facades\Http;

class SecondaryWeatherProvider implements WeatherProviderInterface
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
        // -- Fetch weather data from the secondary weather provider

        $url_location = config('weather.secondary.location_endpoint');
        $url_weather = config('weather.secondary.weather_endpoint');
        $key = config('weather.secondary.api_key');

        $lat = $location['latitude'];
        $lng = $location['longitude'];

        $location_response = Http::retry(3)->get("$url_location?apikey=$key&q=$lat,$lng");

        if ($location_response->getStatusCode() !== 200) {
            return [];
        }

        $location_key = $location_response->json()['Key'];

        $weather_response = Http::retry(3)->get("$url_weather/$location_key?apikey=$key&metric=true");

        if ($weather_response->getStatusCode() !== 200) {
            return [];
        }

        $response = [
            'city'                  => $location_response['ParentCity']['LocalizedName'],
            // 'country'               => $location_response['Country']['ID'],
            'latitude'              => $location_response['GeoPosition']['Latitude'],
            'longitude'             => $location_response['GeoPosition']['Longitude'],
            'observation_time'      => $weather_response[0]['LocalObservationDateTime'],
            'temperature'           => $weather_response[0]['Temperature']['Metric']['Value'],
            'weather_description'   => $weather_response[0]['WeatherText'],
            'weather_icon'          => $weather_response[0]['WeatherIcon'],
            'provider'              => 'secondary',
        ];

        return  $response;
    }
}
