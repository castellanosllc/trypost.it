<script setup>
import { ref, reactive, computed, onMounted, onUnmounted } from "vue";
import { usePage, router } from "@inertiajs/vue3";
import Uppy from "@uppy/core";
import en_US from '@uppy/locales/lib/en_US';
import Url from '@uppy/url';
import Webcam from '@uppy/webcam';
import { COMPANION_URL, COMPANION_ALLOWED_HOSTS } from '@uppy/transloadit';
import Dashboard from "@uppy/dashboard";
import Unsplash from "@uppy/unsplash";
import { createUpload } from "@mux/upchunk";

import DialogModal from "@/Components/DialogModal.vue";
import Button from "@/Components/Button.vue";

import "@uppy/core/dist/style.css";
import "@uppy/dashboard/dist/style.css";
import '@uppy/url/dist/style.min.css';
import '@uppy/webcam/dist/style.min.css';

const props = defineProps({
  maxFileSize: {
    type: Number,
    default: 512 * 1024 * 1024 // 512MB
  },
  allowedFileTypes: {
    type: Array,
    default: () => ["image/*", "video/*"]
  },
  maxNumberOfFiles: {
    type: Number,
    default: 10
  }
});

const emit = defineEmits(['created', 'success', 'error']);

const space = usePage().props.auth.user.current_space;
const show = ref(false);
const areaRef = ref(null);
let uppy = null;

// Estado reativo para gerenciar o upload
const initialState = {
  file: null,
  uploader: null,
  progress: 0,
  uploading: false,
  error: null,
};

const state = reactive({
  ...initialState,
  formattedProgress: computed(() => Math.round(state.progress)),
  reset: () => {
    state.progress = 0;
    state.uploading = false;
    state.error = null;
  },
  fullReset: () => {
    state.file = null;
    state.uploader = null;
    state.progress = 0;
    state.uploading = false;
    state.error = null;
  }
});

// Configurações
const CHUNK_SIZE = 10 * 1024; // 10mb

// Flag para controlar múltiplas inicializações
const isInitializing = ref(false);

// Armazenar os IDs de arquivo e seus uploaders correspondentes
const fileUploaders = ref({});

const initializeUppy = () => {
  if (isInitializing.value) return;
  isInitializing.value = true;

  try {
    if (uppy) {
      try {
        uppy = null;
      } catch (e) {
        console.warn("Erro ao fechar instância anterior do Uppy:", e);
      }
    }

    uppy = new Uppy({
      locale: en_US,
      restrictions: {
        maxNumberOfFiles: props.maxNumberOfFiles,
        allowedFileTypes: props.allowedFileTypes,
        maxFileSize: props.maxFileSize,
        maxNumberOfFiles: 1
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
      .use(Unsplash, {
        companionUrl: COMPANION_URL,
        companionAllowedHosts: COMPANION_ALLOWED_HOSTS,
        companionKeysParams: {
          key: import.meta.env.VITE_UNSPLASH_ACCESS_KEY,
          credentialsName: 'try_post_it_unsplash_creds',
        },
      });

    uppy.on("upload", () => {
      const files = uppy.getFiles();

      if (files.length > 0) {
        // Para cada arquivo, iniciamos um upload em chunks
        files.forEach(file => {
          // Isso irá atualizar o estado do Uppy para mostrar que estamos processando
          uppy.setFileState(file.id, {
            progress: {
              uploadStarted: Date.now(),
              uploadComplete: false,
              percentage: 0,
              bytesUploaded: 0,
              bytesTotal: file.size
            }
          });

          // Inicie o upload em chunks
          uploadFileInChunks(file);
        });
      }
    });
  } catch (error) {
    console.error("Erro ao inicializar Uppy:", error);
  } finally {
    isInitializing.value = false;
  }
};

const uploadFileInChunks = (file) => {
  const fileId = file.id;
  const fileData = file.data;
  const fileName = file.name;
  const fileSize = file.size;

  state.file = fileData;
  state.error = null;
  state.uploading = true;
  state.progress = 0;

  try {
    const uploader = createUpload({
      endpoint: route('medias.chunk', {
        model: 'Space',
        collection: 'media-library',
        visibility: 'public',
        model_id: space.id,
      }),
      headers: {
        "X-CSRF-TOKEN": usePage().props.csrf_token,
      },
      method: "post",
      file: fileData,
      chunkSize: CHUNK_SIZE,
      metadata: {
        fileName: fileName,
        spaceId: space.id,
      }
    });

    // Armazene o uploader para este arquivo
    state.uploader = uploader;
    fileUploaders.value[fileId] = uploader;

    uploader.on("attempt", () => {
      state.error = null;
    });

    uploader.on("progress", (p) => {
      const percentage = p.detail;
      state.progress = percentage;

      // Importante: Atualizar o progresso no Uppy para visualização
      try {
        if (uppy && uppy.getFile(fileId)) {
          const bytesUploaded = Math.floor((fileSize * percentage) / 100);
          uppy.setFileState(fileId, {
            progress: {
              uploadStarted: uppy.getFile(fileId).progress.uploadStarted,
              uploadComplete: false,
              percentage: percentage,
              bytesUploaded: bytesUploaded,
              bytesTotal: fileSize
            }
          });
        }
      } catch (e) {
        console.warn("Erro ao atualizar progresso no Uppy:", e);
      }
    });

    uploader.on("chunkSuccess", (response) => {

      // convert to json
      const data = JSON.parse(response.detail.response.body);

      if (data.finished) {
        emit('created', data.upload.id);

        close();

        router.reload({
          only: ['medias']
        });
      }
    });

    uploader.on("success", (response) => {

      // Marcar como completo no Uppy
      try {
        if (uppy && uppy.getFile(fileId)) {
          uppy.setFileState(fileId, {
            progress: {
              uploadStarted: uppy.getFile(fileId).progress.uploadStarted,
              uploadComplete: true,
              percentage: 100,
              bytesUploaded: fileSize,
              bytesTotal: fileSize
            }
          });
        }
      } catch (e) {
        console.warn("Erro ao finalizar progresso no Uppy:", e);
      }

      // Reset parcial do estado
      state.reset();

      // Limpar o uploader deste arquivo
      delete fileUploaders.value[fileId];

      // Agende uma limpeza completa após um atraso
      setTimeout(() => {
        try {
          state.fullReset();
        } catch (e) {
          console.warn("Erro ao resetar completamente o estado:", e);
        }
      }, 500);
    });

    uploader.on("error", (error) => {
      console.error("Erro no upload:", error);

      // Marcar como erro no Uppy
      try {
        if (uppy && uppy.getFile(fileId)) {
          uppy.setFileState(fileId, {
            error: "Falha no upload",
            progress: {
              ...uppy.getFile(fileId).progress,
              uploadComplete: false
            }
          });
        }
      } catch (e) {
        console.warn("Erro ao definir erro no Uppy:", e);
      }

      // Defina uma mensagem de erro segura
      let errorMessage = "Erro no upload";
      try {
        if (error && error.detail && error.detail.message) {
          errorMessage = error.detail.message;
        }
      } catch (e) {
        console.warn("Erro ao acessar detalhes do erro:", e);
      }

      state.error = errorMessage;

      // Emitir evento de erro
      emit('error', { message: errorMessage });

      // Reset parcial
      state.reset();

      // Limpar o uploader deste arquivo
      delete fileUploaders.value[fileId];

      // Agende uma limpeza completa após um atraso
      setTimeout(() => {
        try {
          state.fullReset();
        } catch (e) {
          console.warn("Erro ao resetar completamente o estado após erro:", e);
        }
      }, 500);
    });

  } catch (e) {
    console.error("Erro ao configurar o upload:", e);
    state.error = "Falha ao iniciar o upload";
    emit('error', { message: "Falha ao iniciar o upload" });

    // Marcar como erro no Uppy
    try {
      if (uppy && uppy.getFile(fileId)) {
        uppy.setFileState(fileId, {
          error: "Falha ao iniciar o upload"
        });
      }
    } catch (err) {
      console.warn("Erro ao definir estado de erro inicial:", err);
    }

    state.reset();
  }
};

const cancel = () => {
  try {
    // Cancelar todos os uploads ativos
    Object.values(fileUploaders.value).forEach(uploader => {
      try {
        uploader.abort();
      } catch (e) {
        console.warn("Erro ao abortar um uploader:", e);
      }
    });

    fileUploaders.value = {};

    if (state.uploader) {
      state.uploader.abort();
    }
  } catch (e) {
    console.warn("Erro ao abortar upload:", e);
  }

  state.reset();
};

const open = () => {
  show.value = true;

  // Certifique-se de que qualquer estado anterior está limpo
  state.fullReset();
  fileUploaders.value = {};

  // Inicialize o Uppy após um pequeno atraso
  setTimeout(() => {
    initializeUppy();
  }, 300);
};

const close = () => {
  show.value = false;
}

onUnmounted(() => {
  // Certifique-se de limpar todos os recursos
  try {
    // Cancelar todos os uploads ativos
    Object.values(fileUploaders.value).forEach(uploader => {
      try {
        uploader.abort();
      } catch (e) {
        console.warn("Erro ao abortar um uploader durante desmontagem:", e);
      }
    });

    if (state.uploader) {
      state.uploader.abort();
    }
  } catch (e) {
    console.warn("Erro ao abortar upload durante desmontagem:", e);
  }

  try {
    if (uppy) {
      uppy = null;
    }
  } catch (e) {
    console.warn("Erro ao fechar Uppy durante desmontagem:", e);
  }

  // Limpe todas as referências
  uppy = null;
  fileUploaders.value = {};
  state.fullReset();
});

// Expõe a função open para uso externo
defineExpose({
  open
});
</script>

<template>
  <DialogModal max-width="2xl" :show="show" @close="show = false">
    <template #content>
      <div ref="areaRef" />
    </template>
  </DialogModal>
</template>