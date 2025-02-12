<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Weather API Configuration
    |--------------------------------------------------------------------------
    |
    | Here is defined the configuration for the weather API services.
    |
    */

    'primary' => [
        'name' => 'OpenWeatherMap',
        'api_key' => env('OPENWEATHER_API_KEY', ''),
        'endpoint' => env('OPENWEATHER_ENDPOINT', ''),
        'units' => env('OPENWEATHER_UNITS', 'metric'),
        'lang' => env('OPENWEATHER_LANG', 'en'),
        'timeout' => env('OPENWEATHER_TIMEOUT', 5),
    ],

    'secondary' => [
        'name' => 'AccuWeather',
        'api_key' => env('ACCUWEATHER_API_KEY', ''),
        'location_endpoint' => env('ACCUWEATHER_LOCATION_ENDPOINT', ''),
        'weather_endpoint' => env('ACCUWEATHER_WEATHER_ENDPOINT', ''),
        'language' => env('ACCUWEATHER_LANG', 'en-us'),
        'timeout' => env('ACCUWEATHER_TIMEOUT', 5),
    ],
];
