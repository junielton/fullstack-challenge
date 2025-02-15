<template>
  <div class="flex justify-center">
    <div
      v-if="!loaded"
      class="weather-placeholder">
      Loading...
    </div>
    <img
      v-show="loaded"
      :src="imageUrl"
      :alt="weatherDescription"
      @load="handleLoad" />
  </div>
</template>

<script lang="ts" setup>
import { ref, watch, computed } from 'vue'

// @Props ---------------------------------------------------------------------
const props = defineProps<{
  weatherIcon: string
  weatherDescription: string
}>()

// @Data ----------------------------------------------------------------------
const loaded = ref(false)

// @Computed ------------------------------------------------------------------
const imageUrl = computed(() => `https://openweathermap.org/img/wn/${props.weatherIcon}@2x.png`)

// @Watchers ------------------------------------------------------------------
watch(
  () => props.weatherIcon,
  () => {
    loaded.value = false
  }
)

// @Methods -------------------------------------------------------------------
function handleLoad() {
  loaded.value = true
}
</script>

<style scoped>
.weather-placeholder {
  @apply w-[100px] h-[100px] flex items-center justify-center bg-neutral-100 text-neutral-400 rounded-full;
}
</style>
