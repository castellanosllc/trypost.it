<script setup>
import { ref, onMounted, computed } from "vue";
import { usePage, router, Link } from "@inertiajs/vue3";

import { usePosthog } from "@/posthog";
const posthog = usePosthog();

import {
  IconInbox,
  IconAt,
  IconSquareAsterisk,
  IconTelescope,
  IconConfetti,
  IconUsersGroup,
  IconGift,
  IconSettings,
  IconPhoto,
  IconUsers,
  IconWorldShare,
  IconTrendingUp,
  IconMapNorth,
  IconProgressBolt,
  IconLifebuoy,
  IconBook,
  IconLockSquare,
  IconAccessPoint,
  IconHash,
  IconTags,
  IconCalendar
} from "@tabler/icons-vue";


const navigation = [
  {
    group: "Posts",
    items: [
      {
        name: "Posts",
        href: route("posts.index"),
        current: route().current("posts.*"),
        target: "_self",
        icon: IconCalendar,
        show: true,
      },
      {
        name: "Media Library",
        href: route("media-library.index"),
        current: route().current("media-library.*"),
        target: "_self",
        icon: IconPhoto,
        show: true,
      },
    ],
  },
  {
    group: "Tools",
    items: [
      {
        name: "Hashtags",
        href: route("setting.hashtags.index"),
        current: route().current("setting.hashtags.*"),
        icon: IconHash,
        show: true,
      },
      {
        name: "Tags",
        href: route("setting.tags.index"),
        current: route().current("setting.tags.*"),
        target: "_self",
        icon: IconTags,
        show: true,
      },
    ],
  },

  {
    group: "Insights",
    items: [
      {
        name: "Instagram",
        href: route("posts.index"),
        current: route().current("users.*"),
        target: "_self",
        icon: IconUsersGroup,
        show: true,
      },
      {
        name: "Facebook",
        href: route("posts.index"),
        current: route().current("analytics.home"),
        target: "_self",
        icon: IconTrendingUp,
        show: true,
      },
    ],
  },

  {
    group: "Organization",
    items: [
      {
        name: "Settings",
        href: route("posts.index"),
        current: route().current("setting.*"),
        target: "_self",
        icon: IconSettings,
        show: true,
      },
      {
        name: "Accounts",
        href: route("accounts.index"),
        current: route().current("accounts.*"),
        target: "_self",
        icon: IconUsers,
        show: true,
      },
    ],
  },
];

const openLink = (item) => {
  posthog.capture("system:navigation", { property: item.name });

  // open in new tab
  if (item.target == "_blank") {
    return window.open(item.href, "_blank");
  }

  router.visit(item.href);
};
</script>

<template>
  <nav class="flex-1 flex flex-col">
    <ul role="list" class="flex flex-1 flex-col gap-y-6">
      <li>
        <ul role="list" class="-mx-2 space-y-2">
          <li v-for="group in navigation" :key="group">
            <div class="text-sm text-zinc-600 dark:text-white font-medium mb-0.5">
              {{ group.group }}
            </div>
            <div class="space-y-px">
              <ul v-for="item in group.items" :key="item">
                <li @click="openLink(item)" :class="{
                  'bg-zinc-200 dark:bg-zinc-800 text-black dark:text-white':
                    item.current,
                  'text-zinc-900 hover:bg-zinc-200 dark:text-zinc-400 dark:hover:bg-zinc-800 dark:hover:text-white':
                    !item.current,

                  'group flex items-center gap-x-1.5 rounded-lg py-1 px-3 text-sm font-medium cursor-pointer': true,
                  hidden: !item.show,
                }">
                  <component :is="item.icon" class="h-4 w-4 stroke-2" aria-hidden="true" />
                  <div class="flex flex-1 items-center justify-between">
                    <div>{{ item.name }}</div>

                    <span v-if="item.new"
                      class="inline-flex items-center rounded bg-violet-600/10 px-2 py-0.5 text-xs font-medium text-violet-600 dark:text-violet-500 ring-1 ring-inset ring-violet-600/20">
                      New
                    </span>
                  </div>
                </li>

                <div v-if="item.items && item.current" class="py-1">
                  <div class="">
                    <ul v-for="item in item.items" :key="item">
                      <Link :href="item.href" as="li" preserve-scroll preserve-state :class="[
                        'pt-0.5 ml-5 -0 border-l border-zinc-300 dark:border-zinc-700',
                      ]">
                      <div :class="[
                        item.current
                          ? 'text-black dark:text-white'
                          : 'text-zinc-600 hover:bg-zinc-200 dark:text-zinc-400 dark:hover:bg-zinc-800 dark:hover:text-white',
                        'ml-4 group flex gap-x-2 rounded py-1.5 px-1.5 text-sm font-medium cursor-pointer',
                      ]">
                        <div class="flex flex-1 items-center justify-between">
                          <div>
                            {{ item.name }}
                          </div>
                        </div>
                      </div>
                      </Link>
                    </ul>
                  </div>
                </div>
              </ul>
            </div>
          </li>
        </ul>
      </li>
    </ul>
  </nav>
</template>