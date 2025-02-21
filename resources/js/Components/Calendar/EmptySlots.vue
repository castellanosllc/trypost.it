<template>
  <div class="col-start-1 col-end-2 row-start-1 grid grid-cols-1 sm:grid-cols-7 pointer-events-auto relative z-10"
    style="grid-template-rows: repeat(24, minmax(5rem, 1fr));">
    <div v-for="slot in timeSlotGrid" :key="slot.key" :style="{ gridRow: slot.hour + 1, gridColumn: slot.day }"
      class="relative">
      <!-- Slot vazio clicável -->
      <div v-if="!hasEventInSlot(slot.datetime) && !slot.isPast" @click="$emit('add-post', slot.datetime.format())"
        class="absolute inset-0 flex items-center justify-center m-1 opacity-0 hover:opacity-100 hover:bg-zinc-100/50 cursor-pointer group rounded-lg">
        <div class="size-8 rounded-full bg-violet-600/0 group-hover:bg-violet-600/5 flex items-center justify-center">
          <IconPlus class="size-5 text-violet-600/0 group-hover:text-violet-600" />
        </div>
      </div>

      <!-- Slot bloqueado (passado) -->
      <div v-if="slot.isPast" class="absolute inset-0 cursor-not-allowed group">
        <div class="absolute inset-0 bg-zinc-50/20 flex items-center justify-center"
          style="background-image: repeating-linear-gradient(45deg, transparent, transparent 4px, rgba(200, 200, 200, 0.08) 4px, rgba(200, 200, 200, 0.08) 6px);" />
      </div>
    </div>
  </div>
</template>

<script setup>
import { IconPlus } from '@tabler/icons-vue'
import dayjs from '@/dayjs'

const props = defineProps({
  timeSlotGrid: {
    type: Array,
    required: true
  },
  timezone: {
    type: String,
    required: true
  }
})

defineEmits(['add-post'])

const hasEventInSlot = (datetime) => {
  return false // Implemente a lógica de verificação de eventos existentes
}
</script>