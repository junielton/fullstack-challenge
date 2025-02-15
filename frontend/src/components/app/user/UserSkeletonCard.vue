<template>
  <div
    class="card relative bg-gray-100 p-4 rounded-lg flex flex-col items-center justify-center shadow-md hover:shadow-xl hover:-translate-y-1 transition-all duration-700 cursor-pointer">
    <div class="bg-gray-200 p-4 rounded-full shadow-md hover:shadow-xl user-img transition-all duration-1000">
      <UserSvgIcon class="img w-10 h-10 text-gray-500" />
    </div>
    <p class="user-name text-lg font-semibold transition-all duration-1000 w-60 h-3 bg-gray-300 my-3.5"></p>
    <small class="w-40 h-2 bg-gray-200 block"></small>
  </div>
</template>
<script lang="ts" setup>
import UserSvgIcon from '@/components/app/user/UserSvgIcon.vue'
</script>

<style scoped>
.card:hover .user-img {
  transform: translateY(-10px);
}
</style>
