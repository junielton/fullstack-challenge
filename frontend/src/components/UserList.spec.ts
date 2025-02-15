import { mount, flushPromises } from '@vue/test-utils'
import { describe, it, expect, vi, beforeEach, afterEach } from 'vitest'
import UsersList from '../components/UsersList.vue'

// -- Example data for tests
const dummyUsers = [
  { id: 1, name: 'John Doe', email: 'john@example.com', latitude: '0', longitude: '0' },
  { id: 2, name: 'Jane Doe', email: 'jane@example.com', latitude: '1', longitude: '1' }
]

// -- Example weather data (emitted by UserCard)
const dummyWeather = {
  name: 'Sunny',
  weather_icon: '01d',
  weather_description: 'Clear Sky',
  temperature: 25,
  observation_time: new Date(),
  city: 'CityName'
}

describe('UsersList.vue', () => {
  let fetchSpy: any

  beforeEach(() => {
    // -- Mock global.fetch
    fetchSpy = vi.spyOn(global, 'fetch')
  })

  afterEach(() => {
    fetchSpy.mockRestore()
  })

  it('deve renderizar os skeleton cards quando não há usuários', async () => {
    // -- Simulate a successful fetch that returns an empty array
    fetchSpy.mockResolvedValue({
      ok: true,
      json: async () => []
    } as Response)

    const wrapper = mount(UsersList, {
      global: {
        stubs: {
          // Stub simples para os componentes filhos
          UserSkeletonCard: { template: '<div class="user-skeleton-card" />' },
          UserCard: true,
          ModalDialog: {
            template: '<div class="modal-dialog"><slot /></div>',
            props: ['modelValue', 'title'],
            name: 'ModalDialog'
          }
        }
      }
    })

    // -- await fetch
    await flushPromises()

    // -- Check if there is no error message
    expect(wrapper.text()).not.toContain('Erro')

    // -- Where are the skeleton cards?
    const skeletonCards = wrapper.findAll('.user-skeleton-card')
    expect(skeletonCards).toHaveLength(20)
  })

  it('deve exibir mensagem de erro quando a requisição falha', async () => {
    // -- Simulate a failed fetch
    fetchSpy.mockRejectedValue(new Error('Fetch failed'))

    const wrapper = mount(UsersList, {
      global: {
        stubs: {
          UserSkeletonCard: true,
          UserCard: true,
          ModalDialog: {
            template: '<div class="modal-dialog"><slot /></div>',
            props: ['modelValue', 'title'],
            name: 'ModalDialog'
          }
        }
      }
    })

    await flushPromises()

    // -- Check if the error message is displayed
    expect(wrapper.text()).toContain('Fetch failed')
  })

  it('deve renderizar os user cards quando a requisição retorna usuários e abrir o modal ao emitir o evento weather', async () => {
    // -- Simulate a request that resturn users
    fetchSpy.mockResolvedValue({
      ok: true,
      json: async () => dummyUsers
    } as Response)

    const wrapper = mount(UsersList, {
      global: {
        stubs: {
          // -- Stub for UserCard that emits the "weather" event on click
          UserCard: {
            template: `<div class="user-card" @click="$emit('weather', dummyWeather)"></div>`,
            // -- So that the stub knows dummyWeather, we use the "global.mocks" property
            // -- or we can directly reference the dummyWeather variable (already declared above)
            setup() {
              return { dummyWeather }
            }
          },
          UserSkeletonCard: true,
          ModalDialog: {
            name: 'ModalDialog',
            template: '<div class="modal-dialog"><slot /></div>',
            props: ['modelValue', 'title']
          }
        }
      }
    })

    await flushPromises()

    // -- Since the fetch response contains users, the skeleton cards are not rendered
    // -- It searches for the elements that represent the user cards
    const userCards = wrapper.findAll('.user-card')
    expect(userCards).toHaveLength(dummyUsers.length)

    // -- Simulate a click on one of the user cards to emit the "weather" event
    await userCards[0].trigger('click')
    await flushPromises()

    // -- Verify if the modal was opened (modelValue === true)
    const modalDialog = wrapper.findComponent({ name: 'ModalDialog' })
    expect(modalDialog.props('modelValue')).toBe(true)

    // -- Verify if the modal title was updated correctly
    expect(modalDialog.props('title')).toBe(`Weather - ${dummyWeather.name}`)
  })
})
