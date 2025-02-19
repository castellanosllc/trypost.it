<template>
  <div class="flex max-w-full flex-none flex-col sm:max-w-none md:max-w-full">
    <!-- Cabeçalho dos dias da semana -->
    <div ref="containerNav" class="sticky top-0 z-30 flex-none bg-white shadow ring-1 ring-black/5">
      <!-- Mobile -->
      <div class="grid grid-cols-7 text-sm/6 text-gray-500 sm:hidden">
        <button v-for="day in weekDays" :key="day.date" type="button" class="flex flex-col items-center pb-3 pt-2">
          {{ day.shortName }}
          <span :class="[
            'mt-1 flex size-8 items-center3 justify-center font-semibold',
            isToday(day.date) ? 'rounded-full bg-indigo-600 text-white' : 'text-gray-900'
          ]">{{ day.dayOfMonth }}</span>
        </button>
      </div>

      <!-- Desktop -->
      <div
        class="-mr-px hidden grid-cols-7 divide-x divide-gray-100 border-r border-gray-100 text-sm/6 text-gray-500 sm:grid">
        <div class="col-end-1 w-14" />
        <div v-for="day in weekDays" :key="day.date" class="flex items-center justify-center py-2">
          <span :class="{ 'flex items-baseline': isToday(day.date) }">
            {{ day.name }}
            <span :class="[
              'items-center justify-center font-semibold',
              isToday(day.date) ? 'ml-1.5 flex size-6 rounded-full bg-zinc-800 text-white' : 'text-zinc-800'
            ]">{{ day.dayOfMonth }}</span>
          </span>
        </div>
      </div>
    </div>

    <!-- Grid do calendário -->
    <div class="flex flex-auto">
      <div class="sticky left-0 z-10 w-14 flex-none bg-white ring-1 ring-gray-100" />
      <div class="grid flex-auto grid-cols-1 grid-rows-1">

        <!-- Linhas das horas -->
        <TimeGrid :time-slots="timeSlots" :timezone="timezone" ref="containerOffset" />

        <!-- Container para a linha do tempo -->
        <div class="relative col-start-1 col-end-2 row-start-1" style="height: calc(24 * 5rem)">
          <!-- 24 horas * altura do slot -->
          <CurrentTimeLine ref="currentTimeIndicator" :timezone="timezone" />
        </div>

        <!-- Linhas verticais -->
        <VerticalGrid />

        <!-- Grid de Eventos -->
        <EventsGrid :events="events" :timezone="timezone" @edit-post="$emit('edit-post', $event)" />

        <!-- Grid de Slots Vazios -->
        <EmptySlots :time-slot-grid="timeSlotGrid" :timezone="timezone" @add-post="$emit('add-post', $event)" />
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import dayjs from '@/dayjs'
import TimeGrid from '@/components/Calendar/TimeGrid.vue'
import VerticalGrid from '@/components/Calendar/VerticalGrid.vue'
import EventsGrid from '@/components/Calendar/EventsGrid.vue'
import EmptySlots from '@/components/Calendar/EmptySlots.vue'
import CurrentTimeLine from '@/components/Calendar/CurrentTimeLine.vue'

const props = defineProps({
  currentDate: {
    type: Object,
    required: true
  },
  events: {
    type: Array,
    required: true
  },
  timezone: {
    type: String,
    required: true
  }
})

const emit = defineEmits(['edit-post', 'add-post'])

// Refs
const containerNav = ref(null)
const containerOffset = ref(null)
const currentTimeIndicator = ref(null)

// Computed
const weekDays = computed(() => {
  const startOfWeek = props.currentDate.startOf('week')
  return Array.from({ length: 7 }, (_, i) => {
    const date = startOfWeek.add(i, 'day')
    return {
      date: date,
      name: date.format('ddd'),
      shortName: date.format('dd')[0],
      dayOfMonth: date.format('D'),
    }
  })
})

const timeSlots = computed(() => {
  return Array.from({ length: 24 }, (_, i) => {
    const hour = i
    const meridiem = hour < 12 ? 'AM' : 'PM'
    const displayHour = hour === 0 ? 12 : hour > 12 ? hour - 12 : hour
    return {
      hour: hour,
      label: `${displayHour}${meridiem}`
    }
  })
})

const timeSlotGrid = computed(() => {
  const slots = []
  for (let hour = 0; hour < 24; hour++) {
    for (let day = 1; day <= 7; day++) {
      const slotDate = props.currentDate.startOf('week').add(day - 1, 'day')
      const datetime = slotDate.hour(hour).minute(0).second(0)

      slots.push({
        hour,
        day,
        datetime,
        isPast: isSlotInPast(datetime),
        key: `${hour}-${day}`
      })
    }
  }
  return slots
})

// Methods
const isToday = (date) => {
  return date.format('YYYY-MM-DD') === dayjs().tz(props.timezone).format('YYYY-MM-DD')
}

const isSlotInPast = (datetime) => {
  const now = dayjs().tz(props.timezone)
  const slotTime = datetime.tz(props.timezone)

  if (slotTime.format('YYYY-MM-DD') < now.format('YYYY-MM-DD')) {
    return true
  }

  if (slotTime.format('YYYY-MM-DD') === now.format('YYYY-MM-DD')) {
    return slotTime.hour() < now.hour()
  }

  return false
}
</script>
