<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Http\Controllers\WeatherController;
use App\Services\Weather\WeatherService;
use Illuminate\Http\JsonResponse;
use Exception;
use Mockery;

class WeatherControllerTest extends TestCase
{
    /**
     * Tests if the controller returns weather data in the common structure when the parameters are valid.
     */
    public function testShowReturnsCommonStructureWhenParametersAreValid()
    {
        // -- Arrange: latitude and longitude values (passed as strings, as expected by the validation)
        $lat = '-27.097';
        $lng = '-48.911';

        // -- Expected data in the common structure (could come from any provider)
        $expectedData = [
            "city"                => "Brusque",
            "country"             => "BR",
            "latitude"            => -27.097,
            "longitude"           => -48.911,
            "observation_time"    => "2025-02-12T22:14:19+00:00",
            "temperature"         => 27.9,
            "weather_description" => "clear sky",
            "weather_icon"        => "01n",
            "provider"            => "primary"
        ];

        // -- Create a mock for WeatherService
        $weatherServiceMock = Mockery::mock(WeatherService::class);
        $weatherServiceMock->shouldReceive('getWeather')
            ->once()
            ->with([
                'latitude'  => $lat,
                'longitude' => $lng,
            ])
            ->andReturn($expectedData);

        // -- Instantiate the controller with the mocked service
        $controller = new WeatherController($weatherServiceMock);

        // -- Act: call the show method
        $response = $controller->show($lat, $lng);

        // -- Assert: verify that the response is a JsonResponse and that the status is 200
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());

        // -- Retrieve the returned data
        $data = $response->getData(true);

        // -- Verify that the common structure is maintained
        $this->assertArrayHasKey('city', $data);
        $this->assertArrayHasKey('country', $data);
        $this->assertArrayHasKey('latitude', $data);
        $this->assertArrayHasKey('longitude', $data);
        $this->assertArrayHasKey('observation_time', $data);
        $this->assertArrayHasKey('temperature', $data);
        $this->assertArrayHasKey('weather_description', $data);
        $this->assertArrayHasKey('weather_icon', $data);
        $this->assertArrayHasKey('provider', $data);

        // -- Verify that the returned values are as expected
        $this->assertEquals($expectedData, $data);
    }

    /**
     * Tests if a validation error (400) is returned when the parameters are invalid.
     */
    public function testShowReturnsValidationErrorWhenParametersAreInvalid()
    {
        // -- Arrange: empty values for latitude and longitude
        $lat = '';
        $lng = '';

        // -- Create a mock for WeatherService that should not be called
        $weatherServiceMock = Mockery::mock(WeatherService::class);
        $weatherServiceMock->shouldNotReceive('getWeather');

        // -- Instantiate the controller with the mocked service
        $controller = new WeatherController($weatherServiceMock);

        // -- Act: call the show method
        $response = $controller->show($lat, $lng);

        // -- Assert: verify that the response is a JsonResponse and that the status is 400
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(400, $response->getStatusCode());

        $data = $response->getData(true);
        $this->assertArrayHasKey('error', $data);
        $this->assertStringContainsString('Latitude and longitude are required', $data['error']);
    }

    /**
     * Tests if an internal error (500) is returned when WeatherService throws an exception.
     */
    public function testShowReturnsErrorWhenWeatherServiceThrowsException()
    {
        // -- Arrange: valid parameters, but the service will throw an exception
        $lat = '-27.097';
        $lng = '-48.911';
        $exceptionMessage = 'Service unavailable';

        // -- Create a mock for WeatherService that throws an exception when calling getWeather
        $weatherServiceMock = Mockery::mock(WeatherService::class);
        $weatherServiceMock->shouldReceive('getWeather')
            ->once()
            ->with([
                'latitude'  => $lat,
                'longitude' => $lng,
            ])
            ->andThrow(new Exception($exceptionMessage));

        // -- Instantiate the controller with the mocked service
        $controller = new WeatherController($weatherServiceMock);

        // -- Act: call the show method
        $response = $controller->show($lat, $lng);

        // -- Assert: verify that the response is a JsonResponse and that the status is 500
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(500, $response->getStatusCode());

        $data = $response->getData(true);
        $this->assertArrayHasKey('error', $data);
        $this->assertEquals($exceptionMessage, $data['error']);
    }

    /**
     * Finalizes the use of Mockery after each test.
     */
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
