<script setup>
import { onMounted, ref, watch, nextTick } from "vue";

const props = defineProps({
  modelValue: String,
  resize: {
    type: Boolean,
    default: false,
  },
  rows: {
    type: Number,
    default: 2,
  },
});

defineEmits(["update:modelValue"]);

const input = ref(null);

const autoResize = () => {
  if (!props.resize || !input.value) return; // 🔹 Só ajusta se `resize` for `true`

  input.value.style.height = "auto"; // 🔹 Reseta para evitar crescimento infinito
  input.value.style.height = `${input.value.scrollHeight}px`; // 🔹 Ajusta a altura conforme o conteúdo
};

onMounted(() => {
  if (input.value.hasAttribute("autofocus")) {
    input.value.focus();
  }

  nextTick(() => {
    autoResize(); // 🔹 Ajusta altura inicial se `resize` for `true`
  });
});

// 🔹 Atualiza a altura sempre que o conteúdo mudar
watch(() => props.modelValue, () => {
  autoResize();
});

defineExpose({ focus: () => input.value.focus() });
</script>

<template>
  <textarea class="w-full form-input" :value="modelValue" @input="$emit('update:modelValue', $event.target.value)"
    ref="input" :rows="resize ? rows : undefined" />
</template>