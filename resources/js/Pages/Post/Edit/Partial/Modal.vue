<script setup>
import Modal from "@/Components/Modal.vue";

const emit = defineEmits(["close"]);

defineProps({
  show: {
    type: Boolean,
    default: false,
  },
  maxWidth: {
    type: String,
    default: "2xl",
  },
  closeable: {
    type: Boolean,
    default: true,
  },
});

const close = () => {
  emit("close");
};
</script>

<template>
  <Modal :show="show" :max-width="maxWidth" :closeable="closeable" @close="close">
    <div class="w-full">
      <div class="text-lg font-medium text-zinc-900 dark:text-zinc-300 px-6 py-4">
        <slot name="title" />
      </div>

      <div class="text-sm text-zinc-600 w-full h-[calc(100vh-10rem)] overflow-y-auto">
        <div class="px-6 py-4">
          <slot name="content" />
        </div>
      </div>
    </div>

    <div v-if="$slots.footer"
      class="flex flex-row justify-end space-x-2 px-6 py-4 bg-white dark:bg-zinc-900 border-t border-zinc-100 dark:border-zinc-800 text-right rounded-b-xl">
      <slot name="footer" />
    </div>
  </Modal>
</template>