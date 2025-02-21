<script setup>
import { ref, onMounted, watch } from "vue";
import { Head, useForm, usePage } from "@inertiajs/vue3";
import draggable from "vuedraggable";
import Button from "@/Components/Button.vue";
import ConfirmDeleteModal from "@/Components/ConfirmDeleteModal.vue";

import { PhX, PhGear, PhDotsSixVertical } from "@phosphor-icons/vue";

import AppLayout from "@/Layouts/Master.vue";
import CreateModal from "./Create.vue";
import EditModal from "./Edit.vue";

const confirmDeleteModal = ref(null);
const createModal = ref(null);
const editModal = ref(null);

const { hashtags } = defineProps({
  hashtags: Object,
});


</script>

<template>

  <Head title="Hashtags" />

  <AppLayout>
    <CreateModal ref="createModal" />
    <EditModal ref="editModal" />

    <ConfirmDeleteModal ref="confirmDeleteModal" description="Are you sure you want to delete this hashtag?" />

    <template #header>
      <div class="w-full">
        <div class="flex items-center justify-between">
          <div class="sm:flex-auto">
            <h1 class="page-title">Hashtags</h1>
          </div>
          <div>
            <Button class="btn-primary" @click="createModal.open()">
              New Hashtag
            </Button>
          </div>
        </div>
      </div>
    </template>

    <div>

      <div v-for="hashtag in hashtags" :key="hashtag.id" class="flex flex-1 items-center space-x-1">
        <div
          class="flex flex-1 items-center justify-between rounded-md px-4 py-2 border border-zinc-100 dark:border-zinc-700">
          <div class="flex flex-1 items-center space-x-4">
            <div class="flex items-center space-x-2">
              <div class="font-medium text-sm text-zinc-600 dark:text-white">
                {{ hashtag.name }}
              </div>
            </div>
          </div>
          <div class="flex items-center space-x-2">
            <Button class="btn-secondary btn-xs space-x-1" @click="editModal.open(hashtag)">
              <PhGear class="h-3 w-3 stroke-2" />
              <div>Edit</div>
            </Button>
            <Button class="btn-secondary btn-xs space-x-1" @click="
              confirmDeleteModal.open({
                url: route('setting.hashtags.destroy', {
                  id: hashtag.id,
                }),
              })
              ">
              <PhX class="h-3 w-3 stroke-2" />
              <div>Delete</div>
            </Button>
          </div>
        </div>
      </div>

    </div>
  </AppLayout>
</template>