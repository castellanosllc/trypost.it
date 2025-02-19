<template>
  <ol class="col-start-1 col-end-2 row-start-1 grid grid-cols-1 sm:grid-cols-7 pointer-events-none relative z-20"
    style="grid-template-rows: repeat(24, minmax(5rem, 1fr));">
    <template v-for="event in events" :key="event.id">
      <li v-bind="getEventStyles(event)" class="pointer-events-auto">
        <div @click="$emit('edit-post', event)"
          class="group absolute inset-0 m-1 flex flex-col overflow-y-auto rounded-lg bg-blue-50 px-3 py-2 text-sm hover:bg-blue-100 cursor-pointer">
          <div class="text-sm line-clamp-1 mb-2 text-zinc-800 font-medium">
            {{ event.content }}
          </div>
          <div class="flex flex-wrap gap-2">
            <Account v-for="postStat in event.post_stats" :key="postStat.id" :account="postStat.account" size="medium"
              :tooltip="true" />
          </div>
        </div>
      </li>
    </template>
  </ol>
</template>

<script setup>
import dayjs from '@/dayjs'
import Account from '@/Components/Account.vue'

const props = defineProps({
  events: {
    type: Array,
    required: true
  },
  timezone: {
    type: String,
    required: true
  }
})

defineEmits(['edit-post'])

const getEventStyles = (event) => {
  const eventDate = dayjs(event.scheduled_at).tz(props.timezone)
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
</script>
