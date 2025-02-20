<script setup>
import { ref, computed } from "vue";
import Button from "@/Components/Button.vue";
import Accordion from "@/Components/Accordion.vue";
import Label from "@/Components/Label.vue";

const props = defineProps({
  post: {
    type: Object,
    required: true
  }
});

const fileInputRef = ref(null);
const isDragging = ref(false);
const isLoading = ref(false);
const error = ref(null);
const dragCounter = ref(0);
const uploadedFiles = ref(props.post.media || []);

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
      formData.append("model", "Post");
      formData.append("model_id", props.post.id);
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
      modelId: props.post.id,
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
  <Accordion :is-open="true">
    <template #title> Media </template>
    <template #content>
      <div class="col-span-6">
        <input type="file" ref="fileInputRef" @change="handleFileSelect" accept="image/*" multiple class="hidden" />

        <!-- Preview Grid -->
        <div v-if="uploadedFiles.length > 0" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
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

        <!-- Área de Drag and Drop -->
        <div class="mt-4 border-2 border-dashed rounded-lg p-6 text-center cursor-pointer transition-colors" :class="{
          'border-gray-300 hover:border-gray-400': !isDragging,
          'border-blue-500 bg-blue-50': isDragging,
          'opacity-50 cursor-not-allowed': isLoading
        }" @click="fileInputRef.click()" @dragenter="handleDragEnter" @dragleave="handleDragLeave"
          @dragover="handleDragOver" @drop="handleDrop">
          <div class="space-y-2">
            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48"
              aria-hidden="true">
              <path
                d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
            <div class="text-sm text-gray-600">
              <span v-if="isDragging">Solte as imagens aqui</span>
              <span v-else>Arraste e solte imagens aqui, ou clique para selecionar</span>
            </div>
            <p class="text-xs text-gray-500">
              Você pode selecionar múltiplas imagens
            </p>
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
  </Accordion>
</template>
