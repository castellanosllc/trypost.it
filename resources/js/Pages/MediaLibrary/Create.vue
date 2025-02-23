<script setup>
import { ref, watch, onUnmounted } from "vue";
import { usePage } from "@inertiajs/vue3";
import Uppy from "@uppy/core";
import en_US from '@uppy/locales/lib/en_US';
import Url from '@uppy/url';
import Webcam from '@uppy/webcam';
import { COMPANION_URL, COMPANION_ALLOWED_HOSTS } from '@uppy/transloadit';
import Dashboard from "@uppy/dashboard";
import XHRUpload from "@uppy/xhr-upload";
import Unsplash from "@uppy/unsplash";

import DialogModal from "@/Components/DialogModal.vue";
import Button from "@/Components/Button.vue";


import "@uppy/core/dist/style.css";
import "@uppy/dashboard/dist/style.css";
import '@uppy/url/dist/style.min.css';
import '@uppy/webcam/dist/style.min.css';

const props = defineProps({
  space: {
    type: Object,
    required: true,
  },
});

const show = ref(false);
const error = ref(null);
const isLoading = ref(false);
const areaRef = ref(null);
let uppy = null;

const initializeUppy = () => {

  // reset
  uppy = null;

  uppy = new Uppy({
    locale: en_US,
    restrictions: {
      maxNumberOfFiles: 10,
      allowedFileTypes: ["image/*", "video/*"],
    },
    autoProceed: false,
  })
    .use(Dashboard, {
      inline: true,
      target: areaRef.value,
      replaceTargetContent: true,
      showProgressDetails: true,
      proudlyDisplayPoweredByUppy: false,
    })
    .use(Webcam)
    // .use(Url, {
    //   companionUrl: COMPANION_URL,
    //   companionAllowedHosts: COMPANION_ALLOWED_HOSTS,
    // })
    .use(XHRUpload, {
      endpoint: route("medias.store"),
      fieldName: "media",
      formData: true,
      headers: {
        Accept: "application/json",
        "X-CSRF-TOKEN": usePage().props.csrf_token,
      },
      bundle: false, // Importante para enviar cada arquivo individualmente!
    })
    .use(Unsplash, {
      companionUrl: COMPANION_URL,
      companionAllowedHosts: COMPANION_ALLOWED_HOSTS,
      companionKeysParams: {
        key: import.meta.env.VITE_UNSPLASH_ACCESS_KEY,
        credentialsName: 'try_post_it_unsplash_creds',
      },
    });

  uppy.on("file-added", async (file) => {
    if (file.source === "Url" || file.source === "Unsplash") {
      try {
        const fileUrl = file.remote?.url || file.preview; // 🔹 Garante que a URL é válida

        console.log(`Baixando arquivo da URL: ${fileUrl}`);

        // 🔹 Baixa a mídia da URL fornecida pelo usuário
        const response = await fetch(fileUrl, { mode: "cors" });

        if (!response.ok) {
          throw new Error(`Erro ao baixar mídia: ${response.statusText}`);
        }

        const blob = await response.blob(); // Converte para Blob
        const extension = file.extension || (blob.type.includes("video") ? "mp4" : "jpg");



        const formData = new FormData();
        formData.append("media", blob, `${file.id}.${extension}`);
        formData.append("model", "Space");
        formData.append("model_id", props.space.id);
        formData.append("collection", "media-library");
        formData.append("visibility", "public");

        await axios.post(route("medias.store"), formData, {
          headers: { "X-CSRF-TOKEN": usePage().props.csrf_token },
        });

        uppy.removeFile(file.id); // Remove do Uppy após upload
      } catch (err) {
        console.error("Erro ao baixar/enviar imagem do Unsplash:", err);
        error.value = "Erro ao enviar imagem do Unsplash.";
      }
    } else {
      uppy.setFileMeta(file.id, {
        model: "Space",
        model_id: props.space.id,
        collection: "media-library",
        visibility: "public",
      });
    }
  });

  uppy.on("upload-success", (file, response) => {
    if (response.body && response.body.url) {
      // uploadedFiles.value.push(response.body);
    }
  });

  uppy.on("upload-error", (file, error) => {
    console.error("Erro no upload:", error);
    error.value = "Erro ao fazer upload da imagem.";
  });

  uppy.on("complete", (result) => {
    if (result.failed.length > 0) {
      error.value = "Algumas imagens falharam no upload.";
    }
    isLoading.value = false;
    show.value = false;
  });
};

const open = () => {
  show.value = true;

  setTimeout(() => {
    initializeUppy();
  }, 100);
}

defineExpose({
  open
})
</script>

<template>
  <DialogModal max-width="2xl" :show="show" @close="show = false">

    <template #content>
      <div ref="areaRef"></div>

      <!-- Mensagem de erro -->
      <p v-if="error" class="mt-2 text-sm text-red-600">
        {{ error }}
      </p>
    </template>
  </DialogModal>
</template>