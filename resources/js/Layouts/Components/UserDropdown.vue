<script setup>
import { ref, onMounted } from "vue";
import { usePage, Link, router } from "@inertiajs/vue3";

import {
  IconCheck,
  IconUser,
  IconChevronDown,
  IconLogout,
  IconBell,
} from "@tabler/icons-vue";

import { Menu, MenuButton, MenuItem, MenuItems } from "@headlessui/vue";


const user = usePage().props.auth.user;
const currentSpace = usePage().props.auth.user.current_space;
const spaces = usePage().props.auth.user.spaces;

const switchToSpace = (space) => {
  router.put(
    route("spaces.update-current"),
    {
      space_id: space.id,
    },
    {
      preserveState: false,
    }
  );
};

</script>
<template>
  <div>
    <div class="flex items-center justify-between">
      <Menu as="div" class="relative flex-1">
        <div>
          <MenuButton
            class="inline-flex w-full items-center justify-between gap-x-1.5 py-1 px-2 text-sm font-medim border border-zinc-200 dark:border-zinc-700 rounded-lg text-zinc-500 dark:text-zinc-200 hover:text-black dark:hover:text-white hover:bg-zinc-50 dark:hover:bg-zinc-800">
            <div class="flex flex-shrink items-center space-x-2">
              <div class="w-6 h-6 rounded bg-zinc-200 dark:bg-zinc-700 inline-flex items-center justify-center">
                <span v-if="currentSpace.name" class="text-xs font-medium leading-none text-zinc-800 dark:text-white">
                  {{ currentSpace.name.charAt(0) }}
                </span>
              </div>

              <div class="text-left truncate font-medium max-w-[100px]">
                {{ currentSpace.name }}
              </div>
            </div>
            <IconChevronDown class="h-4 w-4 text-zinc-400 stroke-2" aria-hidden="true" />
          </MenuButton>
        </div>

        <transition enter-active-class="transition ease-out duration-100"
          enter-from-class="transform opacity-0 scale-95" enter-to-class="transform opacity-100 scale-100"
          leave-active-class="transition ease-in duration-75" leave-from-class="transform opacity-100 scale-100"
          leave-to-class="transform opacity-0 scale-95">
          <MenuItems
            class="fixed top-0 z-40 mt-16 w-72 lg:w-56 origin-top-right divide-y divide-zinc-100 dark:divide-zinc-700 rounded-lg bg-white dark:bg-zinc-800 shadow-2xl focus:outline-none border border-zinc-200 dark:border-zinc-700">
            <div class="py-1">
              <div class="block px-4 py-2 text-xs text-zinc-400 font-medium">
                {{ user.email }}
              </div>
              <MenuItem v-slot="{ active }">
              <Link :href="route('setting.account.edit')" :class="[
                active
                  ? 'bg-zinc-100 dark:bg-zinc-900 text-zinc-900 dark:text-zinc-300'
                  : 'text-zinc-500 dark:text-zinc-400',
                ' px-4 py-1.5 font-13 w-full text-left flex items-center space-x-2 font-medium',
              ]">
              <IconUser class="w-4 h-4 stroke-2" />
              <div>My Account</div>
              </Link>
              </MenuItem>
            </div>
            <div class="py-1 max-h-52 overflow-y-auto">
              <MenuItem v-for="space in spaces" :key="space" v-slot="{ active }">
              <div @click="switchToSpace(space)" :class="[
                active
                  ? 'bg-zinc-100 dark:bg-zinc-900 text-zinc-900 dark:text-zinc-300'
                  : 'text-zinc-500 dark:text-zinc-400',
                'cursor-pointer px-4 py-1.5 font-13 w-full text-left flex items-center space-x-2',
              ]">
                <div class="flex flex-1 items-start min-w-0">
                  <div class="flex items-center flex-1 space-x-2 min-w-0 mr-4">
                    <div class="flex-none">
                      <div class="w-8 h-8 rounded bg-zinc-200 dark:bg-zinc-700 inline-flex items-center justify-center">
                        <span v-if="space.name" class="text-xs font-medium leading-none text-zinc-800 dark:text-white">
                          {{
                            space.name.charAt(
                              0
                            )
                          }}
                        </span>
                      </div>
                    </div>

                    <div class="min-w-0">
                      <div class="truncate font-medium text-zinc-800 dark:text-zinc-200">
                        {{ space.name }}
                      </div>
                    </div>
                  </div>
                  <div class="ml-auto" v-if="
                    space.id == user.current_space_id
                  ">
                    <IconCheck class="w-5 h-5 text-green-500" />
                  </div>
                </div>
              </div>
              </MenuItem>
            </div>
            <div class="py-1">
              <MenuItem v-slot="{ active }">
              <Link :href="route('spaces.create')" :class="[
                active
                  ? 'bg-zinc-100 dark:bg-zinc-900 text-zinc-900 dark:text-zinc-300'
                  : 'text-zinc-500 dark:text-zinc-400',
                ' px-4 py-1.5 font-13 w-full text-left flex items-center space-x-2 font-medium',
              ]">
              <div>New Space</div>
              </Link>
              </MenuItem>
            </div>

            <div class="py-1">
              <MenuItem v-slot="{ active }">
              <Link :href="route('logout')" method="post" as="button" :class="[
                active
                  ? 'bg-zinc-100 dark:bg-zinc-900 text-zinc-900 dark:text-zinc-300'
                  : 'text-zinc-500 dark:text-zinc-400',
                ' px-4 py-1.5 font-13 w-full text-left flex items-center space-x-2',
              ]">
              <IconLogout class="w-4 h-4 stroke-2" />
              <div>Sign Out</div>
              </Link>
              </MenuItem>
            </div>
          </MenuItems>
        </transition>
      </Menu>
    </div>
  </div>
</template>