# Task List - Fullstack Challenge

## 1. Requirements Analysis and Planning
- ✅ Thoroughly review the challenge instructions.
- ✅ Define the overall application architecture (Backend: Laravel; Frontend: VueJS).
- ✅ Identify critical requirements (weather data must be updated every 1 hour and internal API requests must respond within 500ms).

## 2. Setting Up the Development Environment
- ✅ Install Node.js version 18.
- ✅ Configure the Docker environment for the backend (Laravel).
- ✅ Copy the .env.example file to .env.
- ✅ Install PHP dependencies via Composer.
- ✅ Generate the application key using `php artisan key:generate`.
- ✅ Run database migrations with `php artisan migrate`.
- ✅ Execute the seeder to add 20 users with unique locations.

## 3. Building the API with Laravel
- ✅ Initialize the Laravel project.
- ✅ Set up routes and controllers to provide weather data.
- ✅ Ensure internal API requests respond within 500ms. 👀 (first cal counts?)

## 4. Integration with the Weather API
- ✅ Choose a weather API (e.g., OpenWeatherMap or Weather.gov). 💡(Bolth?)
- ✅ Develop the integration with the external weather API.
- ✅ Implement error handling and fallback strategies in case the external API is unavailable.

## 5. Developing the Frontend with VueJS
- ✅ Set up the VueJS environment and install necessary npm dependencies.
- ✅ Build the landing page to list users with their current weather data.
- ✅ Implement functionality to open a modal or new page with detailed weather information when a user is clicked.
- ✅ (Optional) Integrate a UI library (e.g., Vuetify, Tailwind, Bootstrap) to enhance the presentation.

## 6. Testing and Optimization
- ✅ Write automated tests for both the backend and frontend.
- 👀 Implement caching using Redis to optimize API responses. (database)
- ✅ Consider using queues and workers for asynchronous operations.
- ✅ Monitor and optimize the application performance to ensure fast responses. ~60ms~

## 7. Documentation and Delivery
- ✅ Document the development process and the technical decisions made.
- [ ] Send the cloned repository link to the interviewer.
- [ ] Report the total time spent completing the challenge.

---

## Additional Notes

### Weather API Selection
For this challenge, I will choose the OpenWeatherMap API as the primary weather data provider. OpenWeatherMap offers a free tier with a reasonable number of requests per minute, making it suitable for this project. Additionally, it provides a wide range of weather data, including current weather, forecasts, and historical data. As a secondary provider, I will consider using the AccuWeather API, which offers similar features and data quality.

### Application Architecture
```
app/
├── Contracts/
│   └── WeatherProviderInterface.php
├── Jobs/
│   └── UpdateWeatherJob.php
├── Services/
│   └── Weather/
│       ├── PrimaryWeatherProvider.php
│       ├── SecondaryWeatherProvider.php
│       └── WeatherService.php
├── Http/
│   └── Controllers/
│       └── WeatherController.php
config/
└── weather.php
```

### Observations
- The response from the apis are pretty different, so I will need to create a common data structure to handle the data.

**Common Data Structure**
```json
{
   "city": "Brusque",
   "country": "BR",
   "latitude": -27.097,
   "longitude": -48.911,
   "observation_time": "2025-02-12T22:14:19+00:00",
   "temperature": 27.9,
   "weather_description": "clear sky",
   "weather_icon": "01n",
   "provider": "primary"
}
```
```json
{
   "city": "Brusque",
   "country": "BR",
   "latitude": -27.091,
   "longitude": -48.931,
   "observation_time": "2025-02-12T18:55:00-03:00",
   "temperature": 31.8,
   "weather_description": "Clouds and sun",
   "weather_icon": 4,
   "provider": "secondary"
}
```
---

### Time Spent
- Planning and Setup: 2 hours
- Backend Development: 6 hours
- Frontend Development: 5 hours
- Testing and Optimization: 2 hours
- Documentation and Delivery: 1 hour
- **Total Time: 16 hours**

---

### Api Calls examples
- OpenWeatherMap:
	- [data] `api.openweathermap.org/data/2.5/weather?lat=-27.097&lon=-48.911&appid={api_key}&units=metric`

- AccuWeather needs two calls to get the weather data:
	- [key] `http://dataservice.accuweather.com/locations/v1/cities/geoposition/search?apikey={api_key}&q=-27.097,-48.911`
	- [data] `http://dataservice.accuweather.com/currentconditions/v1/2733045?apikey={api_key}`

