<template>
  <dialog
    ref="modalRef"
    @close="handleDialogClose"
    class="rounded-md p-6 shadow-lg">
    <div class="flex justify-between items-center mb-4">
      <h2 class="text-lg font-bold">{{ title }}</h2>
      <button
        @click="close"
        class="text-gray-500 hover:text-gray-700 text-2xl">
        &times;
      </button>
    </div>
    <div>
      <slot></slot>
    </div>
  </dialog>
</template>

<script setup lang="ts">
import { ref, watch } from 'vue'

// @Props ---------------------------------------------------------------------
const props = defineProps<{
  modelValue: boolean
  title: string
}>()

// @Emits ---------------------------------------------------------------------
const emit = defineEmits<{ (e: 'update:modelValue', value: boolean): void }>()

// @Data ----------------------------------------------------------------------
const modalRef = ref<HTMLDialogElement | null>(null)

// @Watches -------------------------------------------------------------------
watch(
  () => props.modelValue,
  newValue => {
    if (newValue) {
      modalRef.value?.showModal()
    } else {
      if (modalRef.value?.open) {
        modalRef.value?.close()
      }
    }
  }
)

// @Methods -------------------------------------------------------------------
function close() {
  if (modalRef.value?.open) {
    modalRef.value?.close()
  }
  emit('update:modelValue', false)
}

// ----------------------------------------------------------------------------

function handleDialogClose() {
  if (props.modelValue) {
    emit('update:modelValue', false)
  }
}
</script>
<style scoped>
dialog {
  animation: fade-out 0.4s ease-out;
}

dialog:open {
  animation: fade-in 0.4s ease-out;
}

dialog:open::backdrop {
  @apply bg-black/50;
}

@keyframes fade-in {
  from {
    opacity: 0;
  }
  to {
    opacity: 1;
  }
}

@keyframes fade-out {
  from {
    opacity: 1;
  }
  to {
    opacity: 0;
  }
}
</style>
