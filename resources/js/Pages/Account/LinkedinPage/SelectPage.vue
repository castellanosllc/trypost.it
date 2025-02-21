<script setup>
import { Head, useForm } from "@inertiajs/vue3";

import Button from "@/Components/Button.vue";

const { pages, user } = defineProps({
  pages: Object,
  user: String,
});

const form = useForm({
  page_id: "",
  name: "",
  photo: "",
  user: user,
});

const switchToPage = (page) => {
  form.page_id = page.id;
  form.name = page.name;
  form.photo = page.picture;

  form.post(route("accounts.linkedin-page.store"), {
    preserveState: true,
    preserveScroll: true,
  });
};
</script>

<template>

  <Head title="Your Pages" />

  <div class="flex flex-col items-center justify-center min-h-screen bg-zinc-100 dark:bg-zinc-950">
    <div
      class="max-w-lg w-full bg-white dark:bg-zinc-900 rounded-lg px-6 py-6 lg:px-8 border border-zinc-200 dark:border-zinc-700">
      <div class="text-zinc-800 dark:text-zinc-300 text-center text-xl font-semibold mb-6">
        Select the page what you want to use to publish your posts.
      </div>

      <div class="space-y-4">
        <div v-for="page in pages" :key="page.id" @click="switchToPage(page)"
          class="flex items-center justify-between p-4 border border-zinc-200 dark:border-zinc-700 rounded-lg cursor-pointer hover:bg-zinc-50 dark:hover:bg-zinc-800">
          <div class="flex items-center space-x-3">
            <div>
              <img class="w-10 h-10 flex items-center justify-center rounded-full" :src="page.picture" />
            </div>

            <div class="text-sm text-zinc-800 dark:text-zinc-300 font-semibold">
              {{ page.name }}
            </div>
          </div>
        </div>

        <Button class="btn-primary w-full"> Select Page </Button>
      </div>
    </div>
  </div>
</template>