<?php

namespace App\Services\Weather;

use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class WeatherService
{

    public function __construct()
    {
        // Todo: Implement using providers
    }

    /**
     * Fetch weather data for a given location using caching and fallback logic.
     *
     * @param array $location Associative array with keys 'latitude' and 'longitude'
     * @return array Weather data array
     * @throws Exception If both providers fail to return valid data.
     */
    public function getWeather(array $location): array
    {

        Log::info("Fetching weather data for location: " . json_encode($location));

        $primaryProvider = new PrimaryWeatherProvider();
        $secondaryProvider = new SecondaryWeatherProvider();

        $cacheKey = 'weather_' . md5($location['latitude'] . '_' . $location['longitude']);

        // -- Attempt to retrieve weather data from the cache.
        if (Cache::has($cacheKey)) {

            Log::info('From cache');
            return Cache::get($cacheKey);
        };


        // -- If cache is empty, fetch from providers using fallback logic.
        try {
            $weatherData = $primaryProvider->fetchWeather($location);

            if (empty($weatherData)) {
                throw new Exception('Primary provider returned empty data.');
            }

            // -- Update the cache here.
            Cache::put($cacheKey, $weatherData, now()->addMinutes(60));

            return $weatherData;
        } catch (Exception $primaryException) {
            try {
                $weatherData = $secondaryProvider->fetchWeather($location);

                if (empty($weatherData)) {
                    throw new Exception('Secondary provider returned empty data.');
                }

                Cache::put($cacheKey, $weatherData, now()->addMinutes(60));

                return $weatherData;
            } catch (Exception $secondaryException) {
                throw new Exception('Both weather providers failed: ' . $primaryException->getMessage() . ' | ' . $secondaryException->getMessage());
            }
        }
    }
}
