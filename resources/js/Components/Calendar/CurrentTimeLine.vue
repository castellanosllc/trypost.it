<template>
  <div ref="timelineRef" class="absolute inset-x-0 right-0 z-30 pointer-events-none border-t border-violet-300"
    :style="timelineStyle" />
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import dayjs from '@/dayjs'

const props = defineProps({
  timezone: {
    type: String,
    required: true
  }
})

const timelineRef = ref(null)
let updateInterval

const timelineStyle = computed(() => {
  const now = dayjs().tz(props.timezone)
  const hour = now.hour()
  const minute = now.minute()

  // Cada slot tem 5rem (80px) de altura
  const slotHeight = 80
  const totalMinutesInDay = 24 * 60
  const currentMinutes = (hour * 60) + minute

  // Calcula pixels desde o topo
  const top = (currentMinutes / totalMinutesInDay) * (24 * slotHeight)

  return {
    top: `${top}px`
  }
})

const updatePosition = () => {
  if (timelineRef.value) {
    timelineRef.value.style.top = timelineStyle.value.top
  }
}

onMounted(() => {
  updatePosition()
  updateInterval = setInterval(updatePosition, 60000)
})

onUnmounted(() => {
  if (updateInterval) {
    clearInterval(updateInterval)
  }
})
</script>
