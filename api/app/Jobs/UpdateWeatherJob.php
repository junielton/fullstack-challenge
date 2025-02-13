<?php

namespace App\Jobs;

use App\Models\User;
use App\Services\Weather\WeatherService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateWeatherJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    protected User $user;

    /**
     * Create a new job instance.
     *
     * @param User $user The user to update the weather for
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @param WeatherService $weatherService
     * @return void
     */
    public function handle(WeatherService $weatherService)
    {
        // -- Define the location to get the weather for
        $location = [
            'latitude' => $this->user->latitude,
            'longitude' => $this->user->longitude,
        ];

        // -- Fetch the weather data for the location
        $weatherService->getWeather($location);
    }
}
