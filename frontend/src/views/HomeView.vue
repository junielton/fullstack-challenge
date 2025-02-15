<template>
  <div class="min-h-screen">
    <nav class="fixed top-0 bg-white text-2xl font-bold w-full z-10 shadow-xl">
      <div class="max-w-screen-xl mx-auto p-4">
        <h1>Users weather</h1>
      </div>
    </nav>
    <main class="max-w-screen-xl mx-auto p-4 mt-20">
      <hr />
      <UsersList />
    </main>
  </div>
</template>

<script setup lang="ts">
import UsersList from '@/components/UsersList.vue'
</script>
