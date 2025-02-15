<template>
  <button
    @click="fetchUserWeather(latitude, longitude)"
    class="card relative bg-neutral-100 p-4 rounded-lg flex flex-col items-center justify-center shadow-md hover:shadow-xl hover:-translate-y-1 transition-all duration-700 cursor-pointer">
    <div class="bg-neutral-50 p-4 rounded-full shadow-md hover:shadow-xl user-img transition-all duration-1000">
      <UserSvgIcon class="img w-10 h-10 text-neutral-600" />
    </div>
    <p class="user-name text-lg font-semibold transition-all duration-1000">{{ name }}</p>
    <small>{{ email }}</small>
    <span
      v-if="isWeatherLoading"
      class="absolute block top-2 right-2 border-x-2 border-b-2 border-t-2 border-t-transparent rounded-full w-4 h-4 border-neutral-700 animate-spin"></span>
  </button>
</template>
<script lang="ts" setup>
import { ref } from 'vue'
import UserSvgIcon from '@/components/app/user/UserSvgIcon.vue'
import type { IWeatherUser } from '@/interfaces/Weather'

type User = {
  name: string
  email: string
  latitude: string
  longitude: string
}

// @Props ---------------------------------------------------------------------
const props = defineProps<User>()

// @Emits ---------------------------------------------------------------------
const emit = defineEmits<{
  (e: 'weather', value: IWeatherUser): void
  (e: 'error', value: string): void
}>()

// @Data ----------------------------------------------------------------------
const isWeatherLoading = ref(false)

// @Methods -------------------------------------------------------------------
async function fetchUserWeather(latitude: string, longitude: string) {
  const url = `http://localhost/weather/${latitude}/${longitude}`

  isWeatherLoading.value = true

  try {
    const response = await fetch(url)

    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`)
    }

    const data = (await response.json()) as IWeatherUser

    isWeatherLoading.value = false

    emit('weather', { ...data, name: props.name })
  } catch (err) {
    isWeatherLoading.value = false
    const e = err instanceof Error ? err.message : 'Erro desconhecido'
    emit('error', e)
  }
}
</script>

<style scoped>
.card:hover .user-img {
  transform: translateY(-10px);
}
</style>
