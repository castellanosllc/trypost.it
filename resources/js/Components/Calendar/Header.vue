<!-- Header.vue -->
<template>
  <header class="flex flex-none items-center justify-between border-b border-gray-200 px-6 py-4">
    <h1 class="text-base font-semibold text-gray-900">
      <time datetime="2022-01">{{ currentMonthDisplay }}</time>
    </h1>
    <div class="flex items-center">
      <!-- View Toggle -->
      <div class="md:flex md:items-center">
        <button type="button" @click="$emit('toggle-view')"
          class="flex items-center gap-x-1.5 rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
          {{ isWeekView ? 'Week view' : 'Month view' }}
        </button>
      </div>

      <!-- Navigation Controls -->
      <div class="ml-4 relative flex items-center rounded-md bg-white shadow-sm md:items-stretch">
        <button type="button" @click="$emit('navigate', 'prev')"
          class="flex h-9 w-12 items-center justify-center rounded-l-md border-y border-l border-gray-300 pr-1 text-gray-400 hover:text-gray-500 focus:relative md:w-9 md:pr-0 md:hover:bg-gray-50">
          <span class="sr-only">Previous</span>
          <IconChevronLeft class="size-5" aria-hidden="true" />
        </button>

        <button type="button" @click="$emit('navigate', 'today')"
          class="hidden border-y border-gray-300 px-3.5 text-sm font-semibold text-gray-900 hover:bg-gray-50 focus:relative md:block">
          Today
        </button>

        <button type="button" @click="$emit('navigate', 'next')"
          class="flex h-9 w-12 items-center justify-center rounded-r-md border-y border-r border-gray-300 pl-1 text-gray-400 hover:text-gray-500 focus:relative md:w-9 md:pl-0 md:hover:bg-gray-50">
          <span class="sr-only">Next</span>
          <IconChevronRight class="size-5" aria-hidden="true" />
        </button>
      </div>
    </div>
  </header>
</template>

<script setup>
import { IconChevronLeft, IconChevronRight } from '@tabler/icons-vue'

const props = defineProps({
  currentMonthDisplay: {
    type: String,
    required: true
  },
  isWeekView: {
    type: Boolean,
    required: true
  }
})

defineEmits(['toggle-view', 'navigate'])
</script>
