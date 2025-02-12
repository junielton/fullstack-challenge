<?php

namespace App\Contracts;

interface WeatherProviderInterface
{
    /**
     * Fetch weather data for the given location.
     *
     * @param array $location An associative array with keys 'latitude' and 'longitude'
     * @return array An array containing the weather information
     *
     * @throws \Exception if the weather data cannot be retrieved
     */
    public function fetchWeather(array $location): array;
}
