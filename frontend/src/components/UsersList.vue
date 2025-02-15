<template>
  <div class="m-4">
    <div v-if="error">{{ error }}</div>
    <div v-else-if="!users.length">
      <div class="grid sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-5">
        <UserSkeletonCard
          v-for="i in 20"
          :key="i" />
      </div>
    </div>
    <div v-else>
      <div class="grid sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-5">
        <UserCard
          v-for="user in users"
          :key="user.id"
          :email="user.email"
          :latitude="user.latitude"
          :longitude="user.longitude"
          :name="user.name"
          @weather="weather = $event" />
      </div>
    </div>
  </div>

  <ModalDialog
    :modelValue="isModalOpen"
    @update:modelValue="isModalOpen = $event"
    :title="`Weather - ${weather.name}`">
    <div class="text-center w-max min-w-72 sm:min-w-96">
      <img
        v-if="weather.provider === 'primary' && weather.weather_icon"
        :src="`https://openweathermap.org/img/wn/${weather.weather_icon}@2x.png`"
        :alt="weather.weather_description"
        class="w-20 h-20 mx-auto" />
      <h4 class="text-8xl">{{ Math.round(weather.temperature) }}°C</h4>
      <p>{{ weather.weather_description }}</p>
      <hr class="my-4" />
      <p>{{ formatData(weather.observation_time).date }}</p>
      <div class="flex justify-center gap-2">
        <p>{{ formatData(weather.observation_time).day }}</p>
        <p>{{ formatData(weather.observation_time).time }}</p>
      </div>
      <p>{{ formatData(weather.observation_time).period }}</p>

      <h5 class="mt-7 text-2xl">{{ weather.city }}</h5>
    </div>
  </ModalDialog>
</template>
<script lang="ts" setup>
import { onBeforeMount, ref, watch } from 'vue'
import UserCard from './app/user/UserCard.vue'
import UserSkeletonCard from './app/user/UserSkeletonCard.vue'
import ModalDialog from './common/ModalDialog.vue'
import type { IWeatherUser } from '../interfaces/Weather'

interface IUser {
  id: number
  name: string
  email: string
  latitude: string
  longitude: string
}

// @Data ----------------------------------------------------------------------
const error = ref<string | null>(null)
const users = ref<IUser[]>([])
const weather = ref<IWeatherUser>({} as IWeatherUser)
const isModalOpen = ref(false)

// @Watchers ------------------------------------------------------------------
watch(
  () => weather.value,
  newValue => {
    if (Object.keys(newValue).length) {
      isModalOpen.value = true
    }
  }
)

// @Methods -------------------------------------------------------------------
async function fetchUsers() {
  const url = 'http://localhost/users'
  try {
    const response = await fetch(url)

    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`)
    }

    const data = (await response.json()) as IUser[]

    users.value = data
  } catch (err) {
    error.value = err instanceof Error ? err.message : 'Erro desconhecido'
  }
}

// ----------------------------------------------------------------------------

function formatData(data: Date) {
  return {
    date: new Date(data).toLocaleDateString(navigator.language),
    time: new Date(data).toLocaleTimeString(navigator.language),
    day: new Date(data).toLocaleDateString(navigator.language, { weekday: 'long' }),
    period: new Date(data).getHours() >= 6 && new Date(data).getHours() < 18 ? 'Day' : 'Night'
  }
}

// @Lifecycle -----------------------------------------------------------------
onBeforeMount(() => fetchUsers())
</script>
