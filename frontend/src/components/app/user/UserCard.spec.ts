import { mount, flushPromises } from '@vue/test-utils'
import { describe, it, expect, vi } from 'vitest'
import UserCard from './UserCard.vue'

describe('UserCard.vue', () => {
  // Define default props for the component
  const props = {
    name: 'John Doe',
    email: 'john@example.com',
    latitude: '10.0',
    longitude: '20.0'
  }

  it('should emit "weather" event with data when fetch is successful', async () => {
    // Dummy weather data returned from the API
    const dummyWeatherData = {
      weather_icon: '01d',
      weather_description: 'Clear sky',
      temperature: 25,
      observation_time: '2025-02-14T12:00:00Z',
      city: 'Sample City'
    }

    // Mock the global fetch function to return a successful response
    const fetchMock = vi.spyOn(global, 'fetch').mockResolvedValue({
      ok: true,
      json: async () => dummyWeatherData
    } as Response)

    // Mount the component (stub the UserSvgIcon to avoid rendering its internals)
    const wrapper = mount(UserCard, {
      props,
      global: {
        stubs: ['UserSvgIcon']
      }
    })

    // Trigger the click on the button
    await wrapper.find('button').trigger('click')

    // Wait for all pending promises to resolve (e.g., fetch and state updates)
    await flushPromises()

    // Verify that fetch was called with the correct URL
    expect(fetchMock).toHaveBeenCalledWith(`http://localhost/weather/${props.latitude}/${props.longitude}`)

    // Verify that the "weather" event was emitted with the expected payload
    const emitted = wrapper.emitted('weather')
    expect(emitted).toBeTruthy()
    expect(emitted?.[0]).toEqual([{ ...dummyWeatherData, name: props.name }])

    fetchMock.mockRestore()
  })

  it('should emit "error" event when fetch returns a non-OK response', async () => {
    // Mock fetch to return a non-OK response (simulate an HTTP error)
    const fetchMock = vi.spyOn(global, 'fetch').mockResolvedValue({
      ok: false,
      status: 500,
      json: async () => ({})
    } as Response)

    const wrapper = mount(UserCard, {
      props,
      global: {
        stubs: ['UserSvgIcon']
      }
    })

    // Trigger the click on the button
    await wrapper.find('button').trigger('click')
    await flushPromises()

    // Verify that the "error" event was emitted with the error message
    const emitted = wrapper.emitted('error')
    expect(emitted).toBeTruthy()
    expect(emitted?.[0]).toEqual(['HTTP error! status: 500'])

    fetchMock.mockRestore()
  })

  it('should emit "error" event when fetch is rejected', async () => {
    // Simulate a network error by rejecting the fetch promise
    const fetchMock = vi.spyOn(global, 'fetch').mockRejectedValue(new Error('Network error'))

    const wrapper = mount(UserCard, {
      props,
      global: {
        stubs: ['UserSvgIcon']
      }
    })

    // Trigger the click on the button
    await wrapper.find('button').trigger('click')
    await flushPromises()

    // Verify that the "error" event was emitted with the error message
    const emitted = wrapper.emitted('error')
    expect(emitted).toBeTruthy()
    expect(emitted?.[0]).toEqual(['Network error'])

    fetchMock.mockRestore()
  })
})
