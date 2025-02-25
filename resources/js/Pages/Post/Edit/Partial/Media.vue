<script setup>
import { ref, computed, watch } from "vue";
import { router } from "@inertiajs/vue3";
import { IconPhoto, IconX, IconVideo } from "@tabler/icons-vue";


import Button from "@/Components/Button.vue";
import Accordion from "@/Components/Accordion.vue";
import Label from "@/Components/Label.vue";

import MediaLibraryCreate from "@/Pages/MediaLibrary/Create.vue";

const props = defineProps({
  postContent: {
    type: Object,
    required: true
  },
  type: {
    type: String,
    required: true
  },
  maxMedia: {
    type: Number,
    required: true
  },
  allowedFileTypes: {
    type: Array,
    default: () => ["image/*", "video/*"]
  }
});

const mediaLibraryCreate = ref(null);
const medias = ref(props.postContent.media);
const selectedMedia = ref(medias.value[0]);

const addMedia = async (mediaId) => {
  await axios.post(route('medias.copy'), {
    media_id: mediaId,
    model: 'PostContent',
    model_id: props.postContent.id,
    collection: 'medias',
  }).then((response) => {
    medias.value.push(response.data);

    // seleciona a primeira media do array
    selectedMedia.value = response.data;
  });
}

const removeMedia = async (mediaId) => {
  await router.delete(route('medias.destroy', { modelId: props.postContent.id, id: mediaId }));
  // remove media do array
  medias.value = medias.value.filter(media => media.id !== mediaId);

  // seleciona a primeira media do array
  selectedMedia.value = medias.value[0];
}

const removeAllMedias = async () => {
  medias.value.forEach(async (media) => {
    await removeMedia(media.id);
  });
}

watch(
  () => props.type,
  () => {
    console.log('type changed');
    removeAllMedias();
  },
  {
    deep: true,
  }
);

</script>

<template>
  <MediaLibraryCreate ref="mediaLibraryCreate" @created="addMedia" :allowedFileTypes="allowedFileTypes"
    :maxNumberOfFiles="maxMedia" />
  <div class="flex flex-col gap-3">
    <div v-if="selectedMedia" class="relative group w-52 h-52">
      <img v-if="selectedMedia.mime_type.includes('image')" :src="selectedMedia.url"
        class="object-cover rounded-lg w-full h-full" />
      <video v-else-if="selectedMedia.mime_type.includes('video')" :src="selectedMedia.url"
        class="object-cover rounded-lg w-full h-full" controls draggable="false" />
      <button
        class="absolute top-2 right-2 bg-red-500 text-white p-1 rounded-full opacity-0 group-hover:opacity-100 transition-opacity"
        @click.stop="removeMedia(selectedMedia.id)">
        <IconX class="h-4 w-4 stroke-2" />
      </button>
    </div>

    <div v-if="medias.length >= 2" class="flex flex-wrap gap-2">
      <div v-for="media in medias" :key="media.id" @click="selectedMedia = media"
        class="cursor-pointer rounded-lg overflow-hidden w-12 h-12 border-2"
        :class="{ 'border-blue-500': selectedMedia === media, 'border-transparent': selectedMedia !== media }">
        <img v-if="media.mime_type.includes('image')" :src="media.url" class="object-cover w-full h-full" />
        <video v-else-if="media.mime_type.includes('video')" :src="media.url" class="object-cover w-full h-full" />
      </div>
    </div>

    <div v-if="medias.length === 0" class="flex flex-col items-start gap-2">
      <div class="flex flex-col items-center justify-center
               border border-dashed border-zinc-200 dark:border-zinc-800
               rounded-lg p-4 h-52 w-64">
        <IconPhoto class="size-5 stroke-2 mb-4" />
        <div class="text-zinc-400 text-sm">No media found</div>
      </div>
    </div>

    <!-- Media count -->
    <div v-if="type !== 'text' && medias.length >= maxMedia && medias.length >= 2" class="text-xs text-zinc-400">
      {{ medias.length }} / {{ maxMedia }}
    </div>

    <div v-if="type !== 'text' && medias.length < maxMedia">
      <Button class="btn btn-secondary btn-sm rounded" @click="mediaLibraryCreate.open()">
        Add Media
      </Button>
    </div>
  </div>
</template>