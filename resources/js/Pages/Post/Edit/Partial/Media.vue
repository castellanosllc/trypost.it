<script setup>
import { ref, computed } from "vue";
import { router } from "@inertiajs/vue3";
import { IconPhoto } from "@tabler/icons-vue";


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

</script>

<template>
  <MediaLibraryCreate ref="mediaLibraryCreate" @created="addMedia" />
  <div class="flex flex-col gap-4">
    <div v-if="selectedMedia" class="relative group w-52 h-52">
      <img v-if="selectedMedia.mime_type.includes('image')" :src="selectedMedia.url"
        class="object-cover rounded-lg w-full h-full" />
      <video v-else-if="selectedMedia.mime_type.includes('video')" :src="selectedMedia.url"
        class="object-cover rounded-lg w-full h-full" controls draggable="false" />
      <Button class="btn btn-secondary btn-sm rounded absolute top-2 right-2
               opacity-0 group-hover:opacity-100 transition" @click.stop="removeMedia(selectedMedia.id)">
        Remove
      </Button>
    </div>

    <div class="flex flex-wrap gap-2">
      <div v-for="media in medias" :key="media.id" @click="selectedMedia = media" :class="{
        'border-2 border-blue-500': selectedMedia === media,
        'cursor-pointer rounded-lg overflow-hidden': true
      }">
        <img v-if="media.mime_type.includes('image')" :src="media.url" class="object-cover w-12 h-12" />
        <video v-else-if="media.mime_type.includes('video')" :src="media.url" class="object-cover w-12 h-12" />
      </div>
    </div>

    <div v-if="medias.length === 0" class="flex flex-col items-start gap-2">
      <div class="flex flex-col items-center justify-center
               border border-dashed border-zinc-200 dark:border-zinc-800
               rounded-lg p-4 h-52 w-64">
        <IconPhoto class="w-4 h-4 stroke-2" />
        <div class="text-zinc-400">No media found</div>
      </div>
    </div>

    <div v-if="type !== 'text'">
      <Button class="btn btn-secondary btn-sm rounded" @click="mediaLibraryCreate.open()">
        Add Media
      </Button>
    </div>
  </div>
</template>