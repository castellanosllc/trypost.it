<script setup>
import { onMounted, ref, watch, nextTick, computed } from "vue";

const props = defineProps({
  modelValue: String,
  resize: {
    type: Boolean,
    default: false,
  },
  rows: {
    type: Number,
    default: 4,
  },
  maxLength: {
    type: Number,
    default: 512, // Valor padrão de 512 caracteres
  },
  showCounter: {
    type: Boolean,
    default: false,
  },
});

const emit = defineEmits(["update:modelValue"]);

const input = ref(null);

// Função para auto redimensionamento do textarea
const autoResize = () => {
  if (!props.resize || !input.value) return;
  input.value.style.height = "auto";
  input.value.style.height = `${input.value.scrollHeight}px`;
};

// Função para atualizar o valor do campo, respeitando o limite de caracteres
const updateValue = (event) => {
  let newValue = event.target.value;
  if (newValue.length > props.maxLength) {
    newValue = newValue.slice(0, props.maxLength);
  }
  emit("update:modelValue", newValue);
};

const characterCount = computed(() => props.modelValue?.length || 0);
const percentageFilled = computed(() => (characterCount.value / props.maxLength) * 100);

onMounted(() => {
  if (input.value.hasAttribute("autofocus")) {
    input.value.focus();
  }

  nextTick(() => {
    autoResize();
  });
});

watch(() => props.modelValue, () => {
  autoResize();
});

defineExpose({ focus: () => input.value.focus() });
</script>

<template>
  <div class="relative w-full">
    <textarea class="w-full form-input !pb-8 resize-none" :value="modelValue" @input="updateValue" ref="input"
      :rows="resize ? rows : undefined" :maxlength="maxLength" />

    <!-- Contador de caracteres -->
    <div v-if="showCounter" class="absolute bottom-2 right-2 flex items-center justify-center ">
      {{ `${characterCount} / ${maxLength}` }}
    </div>
  </div>
</template>