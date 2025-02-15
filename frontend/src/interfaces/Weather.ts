export interface IWeather {
  city: string
  latitude: -27.097
  longitude: -48.9111
  observation_time: Date
  temperature: number
  weather_description: string
  weather_icon: string
  provider: 'primary' | 'secondary'
}

export interface IWeatherUser extends IWeather {
  name: string
}
