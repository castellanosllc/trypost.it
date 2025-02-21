<script setup>
import { ref, onMounted } from "vue";
import { usePage, Link, router } from "@inertiajs/vue3";

import {
  IconCheck,
  IconUser,
  IconChevronUp,
  IconLogout,
  IconBell,
} from "@tabler/icons-vue";

import { Menu, MenuButton, MenuItem, MenuItems } from "@headlessui/vue";


const user = usePage().props.auth.user;
const currentWorkspace = usePage().props.auth.user.current_workspace;
const workspaces = usePage().props.auth.user.workspaces;

const switchToWorkspace = (workspace) => {
  router.put(
    route("workspaces.update-current"),
    {
      workspace_id: workspace.id,
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
                <span v-if="currentWorkspace.name"
                  class="text-xs font-medium leading-none text-zinc-800 dark:text-white">
                  {{ currentWorkspace.name.charAt(0) }}
                </span>
              </div>

              <div class="text-left truncate font-medium max-w-[100px]">
                {{ currentWorkspace.name }}
              </div>
            </div>
            <IconChevronUp class="h-4 w-4 text-zinc-400 stroke-2" aria-hidden="true" />
          </MenuButton>
        </div>

        <transition enter-active-class="transition ease-out duration-100"
          enter-from-class="transform opacity-0 scale-95" enter-to-class="transform opacity-100 scale-100"
          leave-active-class="transition ease-in duration-75" leave-from-class="transform opacity-100 scale-100"
          leave-to-class="transform opacity-0 scale-95">
          <MenuItems
            class="fixed bottom-0 z-40 mb-16 w-56 origin-top-right divide-y divide-zinc-100 dark:divide-zinc-700 rounded-lg bg-white dark:bg-zinc-800 shadow-2xl focus:outline-none border border-zinc-200 dark:border-zinc-700">
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
              <MenuItem v-for="workspace in workspaces" :key="workspace" v-slot="{ active }">
              <div @click="switchToWorkspace(workspace)" :class="[
                active
                  ? 'bg-zinc-100 dark:bg-zinc-900 text-zinc-900 dark:text-zinc-300'
                  : 'text-zinc-500 dark:text-zinc-400',
                'cursor-pointer px-4 py-1.5 font-13 w-full text-left flex items-center space-x-2',
              ]">
                <div class="flex flex-1 items-start min-w-0">
                  <div class="flex items-center flex-1 space-x-2 min-w-0 mr-4">
                    <div class="flex-none">
                      <div class="w-8 h-8 rounded bg-zinc-200 dark:bg-zinc-700 inline-flex items-center justify-center">
                        <span v-if="workspace.name"
                          class="text-xs font-medium leading-none text-zinc-800 dark:text-white">
                          {{
                            workspace.name.charAt(
                              0
                            )
                          }}
                        </span>
                      </div>
                    </div>

                    <div class="min-w-0">
                      <div class="truncate font-medium text-zinc-800 dark:text-zinc-200">
                        {{ workspace.name }}
                      </div>

                      <div class="text-xs font-medium text-zinc-400 capitalize">
                        {{ workspace.plan.name }}
                      </div>
                    </div>
                  </div>
                  <div class="ml-auto" v-if="
                    workspace.id ==
                    user.current_workspace.id
                  ">
                    <IconCheck class="w-5 h-5 text-green-500" />
                  </div>
                </div>
              </div>
              </MenuItem>
            </div>
            <div class="py-1">
              <MenuItem v-slot="{ active }">
              <Link :href="route('workspaces.create')" :class="[
                active
                  ? 'bg-zinc-100 dark:bg-zinc-900 text-zinc-900 dark:text-zinc-300'
                  : 'text-zinc-500 dark:text-zinc-400',
                ' px-4 py-1.5 font-13 w-full text-left flex items-center space-x-2 font-medium',
              ]">
              <div>New Workspace</div>
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