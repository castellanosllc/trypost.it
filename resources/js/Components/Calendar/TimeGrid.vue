<script setup>
defineProps({
  timeSlots: {
    type: Array,
    required: true
  }
})
</script>


<template>
  <div class="col-start-1 col-end-2 row-start-1 grid divide-y divide-gray-100"
    style="grid-template-rows: repeat(24, minmax(5rem, 1fr))">
    <div ref="containerOffset" class="row-end-1" />
    <div v-for="slot in timeSlots" :key="slot.hour">
      <div class="h-full sticky flex items-center justify-center z-20 -ml-14 w-14 text-right text-[11px] text-gray-400">
        <div>
          {{ slot.label }}
        </div>
      </div>
    </div>
  </div>
</template>
