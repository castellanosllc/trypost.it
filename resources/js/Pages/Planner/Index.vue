<script setup>
import Layout from "@/Layouts/Master.vue";

import { ref, onMounted, computed, watch } from 'vue'
import { IconChevronLeft, IconChevronRight } from '@tabler/icons-vue'
import dayjs from "@/dayjs"
import WeekView from '@/Components/Calendar/WeekView.vue'
import MonthView from '@/Components/Calendar/MonthView.vue'
import Header from '@/Components/Calendar/Header.vue'
import Edit from '@/Pages/Post/Edit.vue'
import Create from '@/Pages/Post/Create.vue'

const timezone = Intl.DateTimeFormat().resolvedOptions().timeZone
const currentView = ref('week')
const currentDate = ref(dayjs())
const events = ref([])
const createModal = ref(null)
const editModal = ref(null)

const currentMonthDisplay = computed(() => {
  return currentDate.value.format('MMMM YYYY')
})

const dateRange = computed(() => {
  if (currentView.value === 'week') {
    const startOfWeek = currentDate.value.startOf('week')
    const endOfWeek = currentDate.value.endOf('week')
    return {
      start: startOfWeek.format('YYYY-MM-DD'),
      end: endOfWeek.format('YYYY-MM-DD')
    }
  } else {
    return {
      start: currentDate.value.startOf('month').format('YYYY-MM-DD'),
      end: currentDate.value.endOf('month').format('YYYY-MM-DD')
    }
  }
})

const toggleView = () => {
  currentView.value = currentView.value === 'week' ? 'month' : 'week'
}

const getPosts = async () => {
  try {
    const response = await axios.get(route('posts.index'), {
      params: dateRange.value
    })
    events.value = response.data
  } catch (error) {
    console.error('Error fetching events:', error)
  }
}

const previousWeek = () => {
  const amount = currentView.value === 'week' ? 'week' : 'month'
  currentDate.value = currentDate.value.subtract(1, amount)
}

const nextWeek = () => {
  const amount = currentView.value === 'week' ? 'week' : 'month'
  currentDate.value = currentDate.value.add(1, amount)
}

const goToToday = () => {
  currentDate.value = dayjs()
}

const addPost = (datetime) => {
  createModal.value.open(datetime)
}

const editPost = (post) => {
  editModal.value.open(post)
}

const handleNavigation = (action) => {
  switch (action) {
    case 'prev':
      previousWeek()
      break
    case 'next':
      nextWeek()
      break
    case 'today':
      goToToday()
      break
  }
}

watch(() => dateRange.value, getPosts, { deep: true })

onMounted(getPosts)
</script>

<template>
  <Layout :overflow="false" :fluid="true">
    <div class="flex h-full flex-col">
      <Header :current-month-display="currentMonthDisplay" :is-week-view="currentView === 'week'"
        @toggle-view="toggleView" @navigate="handleNavigation" />
      <div class="flex-1 overflow-auto">

        <WeekView v-if="currentView === 'week'" :current-date="currentDate" :events="events" :timezone="timezone"
          @edit-post="editPost" @add-post="addPost" />

        <MonthView v-else :current-date="currentDate" :events="events" :timezone="timezone" @edit-post="editPost"
          @add-post="addPost" />
      </div>
    </div>
    <Edit ref="editModal" />
    <Create ref="createModal" />
  </Layout>
</template>
