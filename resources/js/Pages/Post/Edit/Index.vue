<script setup>
import { useForm, usePage, router } from "@inertiajs/vue3";
import { ref, reactive } from "vue";
import { Menu, MenuButton, MenuItem, MenuItems } from '@headlessui/vue'
import { IconChevronUp, IconSend, IconNotes, IconDots } from "@tabler/icons-vue";

import Layout from "@/Layouts/Master.vue";
import ConfirmDeleteModal from "@/Components/ConfirmDeleteModal.vue";
import dayjs from "@/dayjs";

import Form from "./Partial/Form.vue";
import GeneralForm from "./Partial/General.vue";

const confirmDeleteModal = ref(null);

const { post } = defineProps({
  post: {
    type: Object,
    default: null,
  },
});

const form = useForm({
  ...post,
  scheduled_at: dayjs(post.scheduled_at).format("YYYY-MM-DD HH:mm:ss"),
});

const saveDraft = () => {
  form.status = 'draft';
  update();
}

const addToCalendar = () => {
  form.status = 'scheduled';
  update();
}

const update = () => {
  form.put(route("posts.update", { id: post.id }), {
    preserveScroll: true,
  });
};

const clone = () => {
  router.post(route('posts.clone', { id: post.id }));
}
</script>

<template>
  <ConfirmDeleteModal ref="confirmDeleteModal" description="Are you sure you want to delete this post?"
    :preserveState="false" />

  <Layout :fluid="true">
    <!-- <template #title>

    </template> -->

    <template #header>
      <div class="sm:flex sm:items-center">
        <div class="sm:flex-auto">
          <h1 class="page-title">{{ form.status === 'scheduled' ? 'Schedule Post' : 'Draft Post' }}</h1>
        </div>
        <div class="mt-4 sm:mt-0 sm:ml-16 flex space-x-2 sm:flex-none">
          <Menu as="div" class="relative inline-block text-left">
            <div>
              <MenuButton
                class="inline-flex w-full justify-center gap-x-1.5 rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                <IconDots class="-mr-1 size-5 text-gray-400" aria-hidden="true" />
              </MenuButton>
            </div>

            <transition enter-active-class="transition ease-out duration-100"
              enter-from-class="transform opacity-0 scale-95" enter-to-class="transform opacity-100 scale-100"
              leave-active-class="transition ease-in duration-75" leave-from-class="transform opacity-100 scale-100"
              leave-to-class="transform opacity-0 scale-95">
              <MenuItems
                class="absolute right-0 top-10 z-10 mt-2 w-36 origin-top-right rounded-md bg-white shadow-lg ring-1 ring-black/5 focus:outline-none">
                <div class="py-1">
                  <MenuItem v-slot="{ active }">
                  <div @click.prevent="clone()" :class="[active ? 'bg-gray-100 text-gray-900 outline-none' : 'text-gray-700'
                    , 'block px-4 py-2 text-sm']">
                    Clone
                  </div>
                  </MenuItem>
                  <MenuItem v-slot="{ active }">
                  <div @click="
                    confirmDeleteModal.open({
                      url: route('posts.destroy', {
                        id: post.id,
                      }),
                    })" :class="[active ? 'bg-gray-100 text-gray-900 outline-none' : 'text-gray-700'
                      , 'block px-4 py-2 text-sm']">
                    Delete
                  </div>
                  </MenuItem>
                </div>
              </MenuItems>
            </transition>
          </Menu>

          <div class="inline-flex rounded-md shadow-sm">
            <button type="button"
              class="relative inline-flex items-center rounded-l-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-10">
              Schedule
            </button>
            <Menu as="div" class="relative -ml-px block">
              <MenuButton
                class="relative inline-flex items-center rounded-r-md bg-white px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-10">

                <IconChevronUp class="size-5" aria-hidden="true" />
              </MenuButton>
              <transition enter-active-class="transition ease-out duration-100"
                enter-from-class="transform opacity-0 scale-95" enter-to-class="transform opacity-100 scale-100"
                leave-active-class="transition ease-in duration-75" leave-from-class="transform opacity-100 scale-100"
                leave-to-class="transform opacity-0 scale-95">
                <MenuItems
                  class="absolute right-0 top-10 z-10 -mr-1 mt-2 w-36 origin-top-right rounded-md bg-white shadow-lg border border-zinc-200 focus:outline-none">
                  <div class="py-1">
                    <MenuItem v-slot="{ active }">
                    <div @click="addToCalendar"
                      :class="[active ? 'bg-gray-100 text-gray-900 outline-none' : 'text-gray-700', 'flex items-center space-x-1.5 px-3 py-1.5 text-sm font-medium cursor-pointer']">
                      <IconSend class="size-4" />
                      <div>
                        Publish Now
                      </div>
                    </div>
                    </MenuItem>
                    <MenuItem v-slot="{ active }">
                    <div @click="saveDraft"
                      :class="[active ? 'bg-gray-100 text-gray-900 outline-none' : 'text-gray-700', 'flex items-center space-x-1.5 px-3 py-1.5 text-sm font-medium cursor-pointer']">
                      <IconNotes class="size-4" />
                      <div>
                        Save as Draft
                      </div>
                    </div>
                    </MenuItem>

                  </div>
                </MenuItems>
              </transition>
            </Menu>
          </div>


        </div>
      </div>
    </template>

    <div class="max-w-7xl mx-auto">
      <div class="p-4">
        <div class="border-b border-zinc-200 dark:border-zinc-800 pb-4">
          <GeneralForm :form="form" />
        </div>
        <div class="flex flex-col space-y-4 divide-y divide-zinc-200 dark:divide-zinc-800">
          <div v-for="postContent in form.post_contents" :key="postContent.id" class="py-4">
            <Form :post-content="postContent" :account="postContent.account" />
          </div>
        </div>
      </div>
    </div>


  </Layout>
</template>