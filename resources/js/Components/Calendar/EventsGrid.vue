<template>
  <ol class="col-start-1 col-end-2 row-start-1 grid grid-cols-1 sm:grid-cols-7 pointer-events-none relative z-20"
    style="grid-template-rows: repeat(24, minmax(5rem, 1fr));">
    <template v-for="event in events" :key="event.id">
      <li v-bind="getEventStyles(event)" class="pointer-events-auto">
        <Link :href="route('posts.edit', event.id)" preserve-scroll preserve-state :class="{
          'group absolute inset-0 m-1 flex flex-col overflow-y-auto rounded-lg  px-3 py-2 text-sm cursor-pointer':
            true,
          'bg-blue-100/70 hover:bg-blue-100': event.status === 'scheduled',
          'bg-zinc-100/70 hover:bg-zinc-100': event.status === 'draft',
          'bg-green-100/70 hover:bg-green-100': event.status === 'published',
          'bg-red-100/70 hover:bg-red-100': event.status === 'failed',
        }">
        <div class="flex items-start justify-between">
          <div :class="{
            'text-sm line-clamp-1 mb-2 font-medium': true,
            'text-red-600': event.status === 'failed',
            'text-blue-600': event.status === 'scheduled',
            'text-green-600': event.status === 'published',
            'text-zinc-600': event.status === 'draft',
          }" v-tooltip="{ content: event.post_contents[0]?.content }">
            {{ event.post_contents[0]?.content }}
          </div>
          <div class="flex items-center" v-tooltip="{ content: event.status }">
            <IconCircleX class="h-[18px] w-[18px] text-red-500 flex-none" v-if="event.status === 'failed'" />
            <IconCircleDashed class="h-[18px] w-[18px] text-zinc-500 flex-none" v-if="event.status === 'draft'" />
            <IconClock class="h-[18px] w-[18px] text-blue-500 flex-none" v-if="event.status === 'scheduled'" />
            <IconCircleCheck class="h-[18px] w-[18px] text-green-500 flex-none" v-if="event.status === 'published'" />
          </div>
        </div>
        <div class="flex flex-wrap gap-2">
          <Account v-for="postContent in event.post_contents" :key="postContent.id" :account="postContent.account"
            size="small" :tooltip="true" />
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

// Função auxiliar que devolve todos os eventos no mesmo dia/hora de 'event'
function getSlotEvents(event, allEvents, timezone) {
  const date = dayjs(event.scheduled_at).tz(timezone)
  const day = date.day()
  const hour = date.hour()

  return allEvents.filter(e => {
    const eDate = dayjs(e.scheduled_at).tz(timezone)
    return eDate.day() === day && eDate.hour() === hour
  })
}

function getEventStyles(event) {
  const eventDate = dayjs(event.scheduled_at).tz(props.timezone)
  const colStart = eventDate.day() + 1
  const hour = eventDate.hour()

  // Descobre todos os eventos nesse mesmo dia/hora
  const slotEvents = getSlotEvents(event, props.events, props.timezone)
  const totalInSlot = slotEvents.length

  // Descobre a posição do evento atual dentro desses eventos
  const indexInSlot = slotEvents.findIndex(e => e.id === event.id)

  // Divide o espaço igualmente e aplica deslocamento
  return {
    class: 'relative mt-px flex hidden sm:flex',
    style: {
      // Define a linha/hora
      gridRow: hour + 1,
      // Define a coluna/dia
      gridColumn: colStart,
      // Largura = fração do espaço, se houver mais de um evento
      width: `calc(100% / ${totalInSlot})`,
      // Offset na esquerda
      left: `calc(${indexInSlot} * (100% / ${totalInSlot}))`
    }
  }
}
</script>