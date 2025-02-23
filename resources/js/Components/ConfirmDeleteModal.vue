<script setup>
import { ref } from "vue";
import { useForm } from "@inertiajs/vue3";
import Modal from "@/Components/Modal.vue";
import Button from "@/Components/Button.vue";

const emit = defineEmits(["deleted"]);

const isOpen = ref(false);
const deleteForm = useForm({});

const url = ref("");

const props = defineProps({
  title: {
    type: String,
    default: "Are you sure?",
  },

  description: {
    type: String,
    default:
      "Are you sure you want to perform this action? This action cannot be undone.",
  },

  action: {
    type: String,
    default: "Delete",
  },

  preserveState: {
    type: Boolean,
    default: false,
  },
});

const remove = () => {
  deleteForm.delete(url.value, {
    preserveScroll: true,
    preserveState: props.preserveState,
    onSuccess: () => close(),
  });
};

const open = (data) => {
  url.value = data.url;
  isOpen.value = true;
};

const close = () => {
  url.value = "";
  isOpen.value = false;
  emit("deleted");
};

defineExpose({
  open,
  close,
});
</script>

<template>

  <Modal :show="isOpen" :closeable="false" @close="close" :z-index="60" max-width="sm">
    <div class="bg-white dark:bg-zinc-900 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
      <div class="sm:flex sm:items-start w-full">
        <div class="sm:mt-0 w-full">
          <h3 class="text-lg text-center font-semibold text-zinc-800 dark:text-zinc-300">
            {{ title }}
          </h3>

          <div class="mt-4 text-sm text-center text-zinc-600 dark:text-zinc-400">
            {{ description }}
          </div>
        </div>
      </div>
    </div>

    <div
      class="flex flex-row justify-end px-6 py-4 border-t border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-right">
      <Button @click="close()" class="btn-secondary !px-6">
        Cancel
      </Button>

      <Button class="ml-3" :class="{
        'opacity-25': deleteForm.processing,
        'btn-danger w-full': true,
      }" :disabled="deleteForm.processing" @click="remove">
        {{ action }}
      </Button>
    </div>
  </Modal>
</template>