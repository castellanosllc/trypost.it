<script setup>
import { ref, computed } from "vue";
import Button from "@/Components/Button.vue";
import Accordion from "@/Components/Accordion.vue";
import Label from "@/Components/Label.vue";

const props = defineProps({
  postContent: {
    type: Object,
    required: true
  }
});

const fileInputRef = ref(null);
const isDragging = ref(false);
const isLoading = ref(false);
const error = ref(null);
const dragCounter = ref(0);
const uploadedFiles = ref(props.postContent.media || []);

const handleDragEnter = (e) => {
  e.preventDefault();
  dragCounter.value++;
  if (dragCounter.value === 1) {
    isDragging.value = true;
  }
};

const handleDragLeave = (e) => {
  e.preventDefault();
  dragCounter.value--;
  if (dragCounter.value === 0) {
    isDragging.value = false;
  }
};

const handleDragOver = (e) => {
  e.preventDefault();
};

const handleDrop = async (e) => {
  e.preventDefault();
  isDragging.value = false;
  dragCounter.value = 0;

  const files = Array.from(e.dataTransfer.files);
  const imageFiles = files.filter(file => file.type.startsWith('image/'));

  if (imageFiles.length > 0) {
    await uploadFiles(imageFiles);
  } else {
    error.value = 'Por favor, envie apenas arquivos de imagem';
  }
};

const handleFileSelect = async () => {
  const files = Array.from(fileInputRef.value.files);
  if (files.length > 0) {
    await uploadFiles(files);
  }
};

const uploadFiles = async (files) => {
  error.value = null;
  isLoading.value = true;

  try {
    for (const file of files) {
      const formData = new FormData();
      formData.append("media", file);
      formData.append("model", "PostContent");
      formData.append("model_id", props.postContent.id);
      formData.append("collection", "medias");
      formData.append("visibility", "public");

      const { data } = await axios.post(route("medias.store"), formData);
      uploadedFiles.value.push(data);
    }
    error.value = null;
  } catch (err) {
    console.error(err);
    error.value = 'Falha ao fazer upload das imagens';
  } finally {
    isLoading.value = false;
    if (fileInputRef.value) {
      fileInputRef.value.value = null;
    }
  }
};

const deleteMedia = async (mediaId) => {
  try {
    await axios.delete(route("medias.destroy", {
      modelId: props.postContent.id,
      id: mediaId,
    }));

    uploadedFiles.value = uploadedFiles.value.filter(media => media.id !== mediaId);
  } catch (err) {
    console.error(err);
    error.value = 'Falha ao remover imagem';
  }
};
</script>

<template>

  <div class="w-full">
    <input type="file" ref="fileInputRef" @change="handleFileSelect" accept="image/*" multiple class="hidden" />

    <!-- Preview Grid -->
    <div v-if="uploadedFiles.length > 0" class="w-full">
      <div v-for="media in uploadedFiles" :key="media.id" class="relative group">
        <img :src="media.url" :alt="'Imagem ' + media.id" class="w-full h-32 object-cover rounded-lg" />
        <button @click="deleteMedia(media.id)"
          class="absolute top-2 right-2 bg-red-500 text-white p-1 rounded-full opacity-0 group-hover:opacity-100 transition-opacity"
          title="Remover imagem">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>
    </div>

    <!-- Mensagem de erro -->
    <p v-if="error" class="mt-2 text-sm text-red-600">
      {{ error }}
    </p>

    <!-- Botão de Upload -->
    <div class="mt-4">
      <Button class="btn-secondary btn-sm" type="button" :disabled="isLoading" @click="fileInputRef.click()">
        {{ isLoading ? 'Enviando...' : 'Adicionar imagens' }}
      </Button>
    </div>
  </div>

</template>