<template>
  <div class="ring-1 ring-black/5 lg:flex lg:flex-auto lg:flex-col">
    <!-- Cabeçalho dos dias da semana -->
    <div
      class="grid grid-cols-7 gap-px border-b border-gray-300 bg-gray-200 text-center text-xs/6 font-semibold text-gray-700 lg:flex-none">
      <div v-for="day in ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']" :key="day" class="bg-white py-2">
        {{ day }}
      </div>
    </div>

    <!-- Grid do mês -->
    <div class="flex bg-gray-200 text-xs/6 text-gray-700 lg:flex-auto">
      <div class="hidden w-full lg:grid lg:grid-cols-7 lg:grid-rows-5 lg:gap-px">
        <div v-for="(date, index) in monthDays" :key="index"
          :class="[date.isCurrentMonth ? 'bg-white' : 'bg-gray-50 text-gray-500', 'relative px-3 py-2 min-h-[11rem]']">
          <!-- Número do dia -->
          <time :datetime="date.date.format('YYYY-MM-DD')"
            :class="isToday(date.date) ? 'flex size-6 items-center justify-center rounded-full bg-zinc-800 font-semibold text-white' : undefined">
            {{ date.date.format('D') }}
          </time>

          <!-- Lista de eventos do dia -->
          <ol v-if="date.events?.length" class="mt-2">
            <li v-for="event in date.events.slice(0, 2)" :key="event.id" @click="$emit('edit-post', event)"
              class="cursor-pointer">
              <div class="group flex">
                <p class="flex-auto truncate font-medium text-gray-900 group-hover:text-indigo-600">
                  {{ event.content }}
                </p>
                <time :datetime="event.scheduled_at"
                  class="ml-3 hidden flex-none text-gray-500 group-hover:text-indigo-600 xl:block">
                  {{ formatEventTime(event.scheduled_at) }}
                </time>
              </div>
            </li>
            <li v-if="date.events.length > 2" class="text-gray-500">
              + {{ date.events.length - 2 }} more
            </li>
          </ol>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import dayjs from '@/dayjs'

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

const emit = defineEmits(['edit-post'])

// Computed
const monthDays = computed(() => {
  const start = props.currentDate.startOf('month').startOf('week')
  const end = props.currentDate.endOf('month').endOf('week')
  const days = []

  let current = start
  while (current.isBefore(end)) {
    days.push({
      date: current,
      isCurrentMonth: current.month() === props.currentDate.month(),
      events: props.events.filter(event =>
        dayjs(event.scheduled_at).tz(props.timezone).format('YYYY-MM-DD') === current.format('YYYY-MM-DD')
      )
    })
    current = current.add(1, 'day')
  }

  return days
})

// Methods
const isToday = (date) => {
  return date.format('YYYY-MM-DD') === dayjs().tz(props.timezone).format('YYYY-MM-DD')
}

const formatEventTime = (time) => {
  return dayjs(time).tz(props.timezone).format('h:00 A')
}
</script>
