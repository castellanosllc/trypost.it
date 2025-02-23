<script setup>
import { ref, computed } from "vue";
import { IconPhoto } from "@tabler/icons-vue";


import Button from "@/Components/Button.vue";
import Accordion from "@/Components/Accordion.vue";
import Label from "@/Components/Label.vue";

const { medias } = defineProps({
  medias: {
    type: Object,
    required: true
  }
});

const selectedMedia = ref(null);

</script>

<template>

  <div class="w-full">
    <div class="flex items-center">
      <template v-if="medias.length > 0">
        <div v-for="media in medias" :key="media.id">
          <div class="w-12 h-12 rounded-md overflow-hidden">
            <img :src="media.url" class="w-full h-full object-cover" />
          </div>
          <div class="font-medium">
            {{ media.file_name }}
          </div>
        </div>
      </template>
      <template v-else>

        <!-- <div class="text-zinc-400">No media found</div> -->
        <div class="flex flex-col items-start gap-2">
          <div
            class="flex flex-col items-center justify-center border border-dashed border-zinc-200 dark:border-zinc-800 rounded-md p-4 h-52 w-64">
            <IconPhoto class="w-4 h-4 stroke-2" />
            <div class="text-zinc-400">No media found</div>
          </div>
          <Button class="btn btn-secondary btn-sm rounded">Add Media</Button>
        </div>
      </template>
    </div>
  </div>

</template>