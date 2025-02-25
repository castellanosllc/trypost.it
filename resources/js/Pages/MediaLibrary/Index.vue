<script setup>
import { ref, onMounted, watch } from "vue";
import { Head, useForm, usePage } from "@inertiajs/vue3";
import draggable from "vuedraggable";
import Button from "@/Components/Button.vue";
import ConfirmDeleteModal from "@/Components/ConfirmDeleteModal.vue";

import { IconX } from "@tabler/icons-vue";

import AppLayout from "@/Layouts/Master.vue";
import CreateModal from "./Create.vue";

const confirmDeleteModal = ref(null);
const createModal = ref(null);

const { medias } = defineProps({
  medias: Object,
});


</script>

<template>

  <Head title="Media Library" />

  <AppLayout>
    <CreateModal ref="createModal" />

    <ConfirmDeleteModal ref="confirmDeleteModal" description="Are you sure you want to delete this hashtag?" />

    <template #header>
      <div class="w-full">
        <div class="flex items-center justify-between">
          <div class="sm:flex-auto">
            <h1 class="page-title">Media Library</h1>
          </div>
          <div>
            <Button class="btn-primary" @click="createModal.open()">
              Media Upload
            </Button>
          </div>
        </div>
      </div>
    </template>

    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
      <div v-for="media in medias" :key="media.id" class="relative group">
        <div class="relative border border-zinc-100 dark:border-zinc-700 rounded-md overflow-hidden">
          <img v-if="media.mime_type.includes('image')" :src="media.url" class="w-full h-40 object-cover rounded-md"
            loading="lazy" />
          <video v-else-if="media.mime_type.includes('video')" :src="media.url"
            class="w-full h-40 object-cover rounded-md" controls draggable="false" loading="lazy" />
          <div
            class="absolute inset-0 bg-black bg-opacity-50 opacity-0 group-hover:opacity-100 flex flex-col justify-end p-2 transition-opacity">
            <div class="text-white text-sm font-medium">{{ media.file_name }}</div>
            <div class="text-xs text-zinc-300">{{ media.size_formatted }}</div>
          </div>
        </div>

        <button
          class="absolute top-2 right-2 bg-red-500 text-white p-1 rounded-full opacity-0 group-hover:opacity-100 transition-opacity"
          @click="
            confirmDeleteModal.open({
              url: route('medias.destroy', {
                modelId: media.model_id,
                id: media.id,
              }),
            })
            ">
          <IconX class="h-4 w-4 stroke-2" />
        </button>
      </div>
    </div>
  </AppLayout>
</template>