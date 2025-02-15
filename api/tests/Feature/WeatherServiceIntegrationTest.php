<?php

namespace Tests\Feature;

use App\Services\Weather\WeatherService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class WeatherServiceIntegrationTest extends TestCase
{
    public function testWeatherServiceWithPrimaryProvider()
    {
        Http::fake([
            'primary-api-url*' => Http::response([
                'name' => 'Berlin',
                'coord' => ['lat' => 52.52, 'lon' => 13.405],
                'dt' => time(),
                'main' => ['temp' => 25.0],
                'weather' => [['description' => 'Sunny', 'icon' => '01d']]
            ], 200),
        ]);

        $service = new WeatherService();
        $location = ['latitude' => '52.5200', 'longitude' => '13.4050'];

        $data = $service->getWeather($location);

        $this->assertArrayHasKey('temperature', $data);
        $this->assertIsNumeric($data['temperature']);
    }

    public function testWeatherServiceWithFallbackToSecondaryProvider()
    {
        Http::fake([
            'primary-api-url*' => Http::response([], 500),
            'secondary-api-url*' => Http::response([
                ['LocalObservationDateTime' => now(), 'Temperature' => ['Metric' => ['Value' => 22.0]], 'WeatherText' => 'Cloudy', 'WeatherIcon' => '02d']
            ], 200),
        ]);

        $service = new WeatherService();
        $location = ['latitude' => '52.5200', 'longitude' => '13.4050'];

        $data = $service->getWeather($location);

        $this->assertArrayHasKey('temperature', $data);
        $this->assertIsNumeric($data['temperature']);
    }

    public function testWeatherServiceCaching()
    {
        $location = ['latitude' => '52.5200', 'longitude' => '13.4050'];
        $cacheKey = 'weather_' . md5($location['latitude'] . '_' . $location['longitude']);

        Cache::shouldReceive('has')->once()->with($cacheKey)->andReturn(true);
        Cache::shouldReceive('get')->once()->with($cacheKey)->andReturn(['temperature' => 30.0, 'weather_description' => 'Hot']);

        $service = new WeatherService();
        $data = $service->getWeather($location);

        $this->assertIsNumeric($data['temperature']);
    }
}
