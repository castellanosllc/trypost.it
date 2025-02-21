<template>
  <ol class="col-start-1 col-end-2 row-start-1 grid grid-cols-1 sm:grid-cols-7 pointer-events-none relative z-20"
    style="grid-template-rows: repeat(24, minmax(5rem, 1fr));">
    <template v-for="event in events" :key="event.id">
      <li v-bind="getEventStyles(event)" class="pointer-events-auto">
        <Link :href="route('posts.index', event.id)" preserve-scroll preserve-state v-tooltip="{
          placement: 'top',
          html: true,
          content: formatPostPreview(event),
        }" :class="{
          'group absolute inset-0 m-1 flex flex-col overflow-y-auto rounded-lg  px-3 py-2 text-sm cursor-pointer': true,
          'bg-blue-100/70 hover:bg-blue-100': event.status === 'scheduled',
          'bg-zinc-100/70 hover:bg-zinc-100': event.status === 'draft',
          'bg-green-100/70 hover:bg-green-100': event.status === 'published',
          'bg-red-100/70 hover:bg-red-100': event.status === 'failed',
        }">


        <div class="flex items-start justify-start">
          <div :class="{
            'text-sm line-clamp-1 mb-2 font-medium': true,
            'text-red-600': event.status === 'failed',
            'text-blue-600': event.status === 'scheduled',
            'text-green-600': event.status === 'published',
            'text-zinc-600': event.status === 'draft',
          }">
            {{ event.content }}
          </div>
          <IconCircleX class="h-[18px] w-[18px] text-red-500 flex-none" v-if="event.status === 'failed'" />
          <IconCircleDashed class="h-[18px] w-[18px] text-zinc-500 flex-none" v-if="event.status === 'draft'" />
          <IconClock class="h-[18px] w-[18px] text-blue-500 flex-none" v-if="event.status === 'scheduled'" />
          <IconCircleCheck class="h-[18px] w-[18px] text-green-500 flex-none" v-if="event.status === 'published'" />
        </div>
        <div class="flex flex-wrap gap-2">
          <Account v-for="postStat in event.post_stats" :key="postStat.id" :account="postStat.account" size="small"
            :tooltip="true" />
        </div>
        </Link>
      </li>
    </template>
  </ol>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import dayjs from '@/dayjs'
import Account from '@/Components/Account.vue'

import {
  IconCircleDashed,
  IconCircle,
  IconCircleCheck,
  IconCircleX,
  IconInnerShadowRight,
  IconClock,
} from "@tabler/icons-vue";

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

const formatPostPreview = (data) => {
  return `
    <div class="text-xs text-zinc-800">
      ${data.status}
    </div>
  `;
};

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