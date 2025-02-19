<template>
  <div class="flex h-full flex-col">
    <header class="flex flex-none items-center justify-between border-b border-gray-200 px-6 py-4">
      <h1 class="text-base font-semibold text-gray-900">
        <time datetime="2022-01">{{ currentMonthDisplay }}</time>
      </h1>
      <div class="flex items-center">
        <div class="md:flex md:items-center">
          <button type="button" @click="toggleView"
            class="flex items-center gap-x-1.5 rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
            {{ currentView === 'week' ? 'Week view' : 'Month view' }}
          </button>
        </div>
        <div class="ml-4 relative flex items-center rounded-md bg-white shadow-sm md:items-stretch">
          <button type="button" @click="previousWeek"
            class="flex h-9 w-12 items-center justify-center rounded-l-md border-y border-l border-gray-300 pr-1 text-gray-400 hover:text-gray-500 focus:relative md:w-9 md:pr-0 md:hover:bg-gray-50">
            <span class="sr-only">Previous week</span>
            <IconChevronLeft class="size-5" aria-hidden="true" />
          </button>
          <button type="button" @click="goToToday"
            class="hidden border-y border-gray-300 px-3.5 text-sm font-semibold text-gray-900 hover:bg-gray-50 focus:relative md:block">Today</button>
          <span class="relative -mx-px h-5 w-px bg-gray-300 md:hidden" />
          <button type="button" @click="nextWeek"
            class="flex h-9 w-12 items-center justify-center rounded-r-md border-y border-r border-gray-300 pl-1 text-gray-400 hover:text-gray-500 focus:relative md:w-9 md:pl-0 md:hover:bg-gray-50">
            <span class="sr-only">Next week</span>
            <IconChevronRight class="size-5" aria-hidden="true" />
          </button>
        </div>

      </div>
    </header>
    <div ref="container" class="isolate flex flex-auto flex-col overflow-auto bg-white h-screen">
      <div v-if="currentView === 'week'" class="flex max-w-full flex-none flex-col sm:max-w-none md:max-w-full">
        <div class="flex max-w-full flex-none flex-col sm:max-w-none md:max-w-full">
          <div ref="containerNav" class="sticky top-0 z-30 flex-none bg-white shadow ring-1 ring-black/5 sm:pr-8">
            <div class="grid grid-cols-7 text-sm/6 text-gray-500 sm:hidden">
              <button v-for="day in weekDays" :key="day.date" type="button"
                class="flex flex-col items-center pb-3 pt-2">
                {{ day.shortName }}
                <span :class="[
                  'mt-1 flex size-8 items-center3 justify-center font-semibold',
                  isToday(day.date) ? 'rounded-full bg-indigo-600 text-white' : 'text-gray-900'
                ]">{{ day.dayOfMonth }}</span>
              </button>
            </div>

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
          <div class="flex flex-auto">
            <div class="sticky left-0 z-10 w-14 flex-none bg-white ring-1 ring-gray-100" />
            <div class="grid flex-auto grid-cols-1 grid-rows-1">
              <!-- Horizontal lines -->
              <div class="col-start-1 col-end-2 row-start-1 grid divide-y divide-gray-100"
                style="grid-template-rows: repeat(24, minmax(5rem, 1fr))">
                <div ref="containerOffset" class="row-end-1 h-7" />
                <div v-for="slot in timeSlots" :key="slot.hour">
                  <div class="sticky left-0 z-20 -ml-14 -mt-2.5 w-14 pr-2 text-right text-xs/5 text-gray-400">
                    {{ slot.label }}
                  </div>
                </div>
              </div>

              <!-- Vertical lines -->
              <div
                class="col-start-1 col-end-2 row-start-1 hidden grid-cols-7 grid-rows-1 divide-x divide-gray-100 sm:grid sm:grid-cols-7">
                <div class="col-start-1 row-span-full" />
                <div class="col-start-2 row-span-full" />
                <div class="col-start-3 row-span-full" />
                <div class="col-start-4 row-span-full" />
                <div class="col-start-5 row-span-full" />
                <div class="col-start-6 row-span-full" />
                <div class="col-start-7 row-span-full" />
                <div class="col-start-8 row-span-full w-8" />
              </div>

              <!-- Grid de Eventos Existentes -->
              <ol
                class="col-start-1 col-end-2 row-start-1 grid grid-cols-1 sm:grid-cols-7 sm:pr-8 pointer-events-none relative z-20"
                style="grid-template-rows: repeat(24, minmax(5rem, 1fr)); margin-top: 1.75rem">
                <template v-for="event in events" :key="event.id">
                  <li v-bind="getEventStyles(event)" class="pointer-events-auto">
                    <div @click="editPost(event)"
                      class="group absolute inset-0 m-1 flex flex-col overflow-y-auto rounded-lg bg-blue-50 px-3 py-2 text-sm hover:bg-blue-100 cursor-pointer">

                      <div class="text-sm line-clamp-1 mb-2 text-zinc-800 font-medium">
                        {{ event.content }}
                      </div>

                      <div class="flex flex-wrap gap-2">
                        <Account v-for="postStat in event.post_stats" :key="postStat.id" :account="postStat.account"
                          size="medium" :tooltip="true" />
                      </div>
                    </div>
                  </li>
                </template>

                <!-- Adicione logo após a grid de eventos -->
                <div v-if="currentView === 'week'" ref="currentTimeIndicator"
                  class="absolute left-0 right-0 z-30 pointer-events-none" style="border-top: 2px solid #dc2626;">
                  <div class="relative -ml-1 -mt-1">
                    <div class="absolute left-0 -mt-1 size-2 rounded-full bg-red-600"></div>
                  </div>
                </div>
              </ol>



              <!-- Grid de Slots Vazios -->
              <div
                class="col-start-1 col-end-2 row-start-1 grid grid-cols-1 sm:grid-cols-7 sm:pr-8 pointer-events-auto relative z-10"
                style="grid-template-rows: repeat(24, minmax(5rem, 1fr)); margin-top: 1.75rem">
                <div v-for="slot in timeSlotGrid" :key="slot.key"
                  :style="{ gridRow: slot.hour + 1, gridColumn: slot.day }" class="relative">
                  <div v-if="!hasEventInSlot(slot.datetime) && !slot.isPast" @click="addPost(slot.datetime.format())"
                    class="absolute inset-0 flex items-center justify-center opacity-0 hover:opacity-100 hover:bg-gray-50/50 cursor-pointer group">
                    <div
                      class="size-8 rounded-full bg-indigo-600/0 group-hover:bg-indigo-600/10 flex items-center justify-center">
                      <IconPlus class="size-5 text-indigo-600/0 group-hover:text-indigo-600" />
                    </div>
                  </div>
                  <div v-if="slot.isPast" class="absolute inset-0 cursor-not-allowed group">
                    <div class="absolute inset-0 bg-gray-50/20 flex items-center justify-center"
                      style="background-image: repeating-linear-gradient(45deg, transparent, transparent 4px, rgba(200, 200, 200, 0.08) 4px, rgba(200, 200, 200, 0.08) 6px);">
                      <div
                        class="opacity-0 group-hover:opacity-100 transition-opacity bg-gray-800 text-white text-sm py-1 px-2 rounded">
                        Date Past
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div v-if="currentView === 'month'" class="shadow ring-1 ring-black/5 lg:flex lg:flex-auto lg:flex-col">
        <div
          class="grid grid-cols-7 gap-px border-b border-gray-300 bg-gray-200 text-center text-xs/6 font-semibold text-gray-700 lg:flex-none">
          <div v-for="day in ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']" :key="day" class="bg-white py-2">
            {{ day }}
          </div>
        </div>
        <div class="flex bg-gray-200 text-xs/6 text-gray-700 lg:flex-auto">
          <div class="hidden w-full lg:grid lg:grid-cols-7 lg:grid-rows-6 lg:gap-px">
            <div v-for="(date, index) in monthDays" :key="index"
              :class="[date.isCurrentMonth ? 'bg-white' : 'bg-gray-50 text-gray-500', 'relative px-3 py-2 min-h-[8rem]']">
              <time :datetime="date.date.format('YYYY-MM-DD')"
                :class="isToday(date.date) ? 'flex size-6 items-center justify-center rounded-full bg-indigo-600 font-semibold text-white' : undefined">
                {{ date.date.format('D') }}
              </time>
              <ol v-if="date.events?.length" class="mt-2">
                <li v-for="event in date.events.slice(0, 2)" :key="event.id">
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
                <li v-if="date.events.length > 2" class="text-gray-500">+ {{ date.events.length - 2 }} more</li>
              </ol>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <Edit ref="editModal" />
  <Create ref="createModal" />
</template>

<script setup>
import { ref, onMounted, computed, reactive, watch, onUnmounted } from 'vue'
import { usePage, Link, router } from '@inertiajs/vue3'
import { Menu, MenuButton, MenuItem, MenuItems } from '@headlessui/vue'
import { IconChevronDown, IconChevronLeft, IconChevronRight, IconDots, IconPlus } from '@tabler/icons-vue'
import dayjs from "@/dayjs";

import Account from '@/Components/Account.vue'
import Layout from '@/Layouts/Master.vue'
import Button from '@/Components/Button.vue'

import Create from '../Post/Create.vue';
import Edit from '../Post/Edit.vue'

const createModal = ref(null)
const editModal = ref(null)


const posts = ref([])

const range = reactive({
  start: dayjs().startOf('month').format('YYYY-MM-DD'),
  end: dayjs().endOf('month').format('YYYY-MM-DD')
})

const timezone = Intl.DateTimeFormat().resolvedOptions().timeZone;

const currentTimeIndicator = ref(null)
const updateCurrentTimeInterval = ref(null)
const currentView = ref('week')
const container = ref(null);
const containerNav = ref(null);
const containerOffset = ref(null);
const currentDate = ref(dayjs())
const events = ref([])
const loading = ref(false)

const toggleView = () => {
  currentView.value = currentView.value === 'week' ? 'month' : 'week'
}

// Computed properties for date ranges
const dateRange = computed(() => {
  const startOfWeek = currentDate.value.startOf('week')
  const endOfWeek = currentDate.value.endOf('week')
  return {
    start: startOfWeek.format('YYYY-MM-DD'),
    end: endOfWeek.format('YYYY-MM-DD')
  }
})

// Previous computed properties remain the same
const weekDays = computed(() => {
  const startOfWeek = currentDate.value.startOf('week')
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

const currentMonthDisplay = computed(() => {
  return currentDate.value.format('MMMM YYYY')
})

const currentMonthISO = computed(() => {
  return currentDate.value.format('YYYY-MM')
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

const updateCurrentTimePosition = () => {
  if (currentView.value !== 'week') return

  const now = dayjs().tz(timezone)
  console.log('NY Time:', now.format('HH:mm'))

  const currentHour = now.hour()
  const currentMinute = now.minute()
  console.log('Hour:', currentHour, 'Minutes:', currentMinute)

  const position = currentHour + (currentMinute / 60)
  const slotPercentage = 100 / 24
  const topPosition = position * slotPercentage

  console.log('Position:', position, 'Top %:', topPosition)

  if (currentTimeIndicator.value) {
    currentTimeIndicator.value.style.top = `${topPosition}%`
  }
}

const monthDays = computed(() => {
  const start = currentDate.value.tz(timezone).startOf('month').startOf('week')
  const end = currentDate.value.tz(timezone).endOf('month').endOf('week')
  const days = []

  let current = start
  while (current.isBefore(end)) {
    days.push({
      date: current,
      isCurrentMonth: current.month() === currentDate.value.month(),
      events: events.value.filter(event =>
        dayjs(event.scheduled_at).tz(timezone).format('YYYY-MM-DD') === current.format('YYYY-MM-DD')
      )
    })
    current = current.add(1, 'day')
  }

  return days
})

const addPost = async (datetime) => {
  createModal.value.open(datetime)
}

const editPost = async (post) => {
  editModal.value.open(post)
}

// Event fetching
const getPosts = async () => {
  try {
    loading.value = true
    const response = await axios.get(route('posts.index'), {
      params: {
        start: dateRange.value.start,
        end: dateRange.value.end
      }
    })
    events.value = response.data
  } catch (error) {
    console.error('Error fetching events:', error)
  } finally {
    loading.value = false
  }
}

// Watch for week changes
watch(() => dateRange.value, () => {
  getPosts()
}, { deep: true })

const hasEventInSlot = (datetime) => {
  return events.value.some(event => {
    const eventTime = dayjs(event.scheduled_at).tz(timezone)
    return eventTime.hour() === datetime.hour() &&
      eventTime.day() === datetime.day() &&
      eventTime.format('YYYY-MM-DD') === datetime.format('YYYY-MM-DD')
  })
}


// Navigation methods
const previousWeek = () => {
  if (currentView.value === 'week') {
    currentDate.value = currentDate.value.subtract(1, 'week')
  } else {
    currentDate.value = currentDate.value.subtract(1, 'month')
  }
}

const nextWeek = () => {
  if (currentView.value === 'week') {
    currentDate.value = currentDate.value.add(1, 'week')
  } else {
    currentDate.value = currentDate.value.add(1, 'month')
  }
}

const goToToday = () => {
  currentDate.value = dayjs()
}

// Helper methods
const isToday = (date) => {
  return date.format('YYYY-MM-DD') === dayjs().tz(timezone).format('YYYY-MM-DD')
}

const formatEventTime = (time) => {
  return dayjs(time).tz(timezone).format('h:00 A')
}

// Add this helper function to check if a slot is in the past
const isSlotInPast = (datetime) => {
  const now = dayjs().tz(timezone)
  const slotTime = dayjs(datetime).tz(timezone)

  // Se é um dia anterior, está no passado
  if (slotTime.format('YYYY-MM-DD') < now.format('YYYY-MM-DD')) {
    return true
  }

  // Se é hoje, verifica se a hora já passou completamente
  if (slotTime.format('YYYY-MM-DD') === now.format('YYYY-MM-DD')) {
    return slotTime.hour() < now.hour()
  }

  return false
}

// Modify timeSlotGrid to include this information
const timeSlotGrid = computed(() => {
  const slots = []
  for (let hour = 0; hour < 24; hour++) {
    for (let day = 1; day <= 7; day++) {
      const slotDate = currentDate.value.tz(timezone).startOf('week').add(day - 1, 'day')
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


const getEventStyles = (event) => {
  const eventDate = dayjs(event.scheduled_at).tz(timezone)
  const colStart = eventDate.day() + 1
  const hour = eventDate.hour()

  return {
    class: 'relative mt-px flex hidden sm:flex',
    style: {
      gridRow: hour + 1,
      gridColumn: colStart
    }
  }
}


onMounted(() => {
  if (container.value) {
    const now = dayjs().tz(timezone)
    const currentHour = now.hour()
    const rowHeight = container.value.scrollHeight / 24
    const scrollPosition = (currentHour * rowHeight)

    container.value.scrollTop = scrollPosition - (container.value.clientHeight / 3)
  }

  // Initial fetch of events
  getPosts()

  // Inicializa a posição da linha do horário atual
  updateCurrentTimePosition()

  // Atualiza a cada minuto
  updateCurrentTimeInterval.value = setInterval(updateCurrentTimePosition, 60000)
})

onUnmounted(() => {
  // Limpa o intervalo quando o componente é desmontado
  if (updateCurrentTimeInterval.value) {
    clearInterval(updateCurrentTimeInterval.value)
  }
})
</script>
