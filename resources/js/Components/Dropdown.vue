<script setup>
import { ref, watch, computed } from "vue";
import {
  Listbox,
  ListboxButton,
  ListboxOption,
  ListboxOptions,
} from "@headlessui/vue";

import {
  IconChevronUp,
  IconChevronDown,
  IconCheck,
  IconSearch,
} from "@tabler/icons-vue";

const emit = defineEmits(["update:modelValue"]);

const { modelValue, options, placeholder, multiple, maxSelection } =
  defineProps({
    modelValue: {
      type: [String, Number, Boolean, Array],
      required: false,
    },

    multiple: {
      type: Boolean,
      default: false,
    },

    options: {
      type: Array,
      required: true,
    },

    placeholder: {
      type: String,
      default: "Select...",
    },

    search: {
      type: Boolean,
      default: false,
    },

    button: {
      type: Boolean,
      default: true,
    },

    icon: {
      type: Boolean,
      default: false,
    },

    i18n: {
      type: Boolean,
      default: false,
    },

    maxSelection: {
      type: Number,
      required: false,
      default: null,
    },

    align: {
      type: String,
      default: "left",
    },
  });

const listboxButton = ref(null);
const dropdownPosition = ref({ top: "0px", left: "0px", width: "0px" });

const input = ref(null);
const query = ref("");
const selected = ref(
  multiple
    ? options.filter((o) => modelValue.includes(o.id))
    : options.find((o) => o.id === modelValue)
);

const o = [...options];

const filtered = computed(() => {
  return query.value === ""
    ? o
    : o.filter((op) => {
      return op.label.toLowerCase().includes(query.value.toLowerCase());
    });
});

const setFocus = (open) => {
  if (open) {
    setTimeout(() => {
      input.value?.focus();
    }, 200);
  }
};

const updateDropdownPosition = () => {
  if (listboxButton.value) {
    const buttonRect = listboxButton.value.getBoundingClientRect();
    dropdownPosition.value = {
      top: `${buttonRect.bottom + window.scrollY}px`,
      left: `${buttonRect.left + window.scrollX}px`,
      width: `${buttonRect.width}px`,
    };
  }
};

watch(
  () => selected.value,
  (value, oldValue) => {
    updateDropdownPosition();
    if (multiple) {
      if (maxSelection && value.length > maxSelection) {
        // Reset to the old value and provide feedback
        selected.value = oldValue;
        alert(`You can select up to ${maxSelection} items.`);
        return;
      }

      emit(
        "update:modelValue",
        value.map((o) => o.id)
      );
      return;
    }

    emit("update:modelValue", value.id);
    query.value = "";
  }
);

watch(
  () => query.value,
  () => {
    updateDropdownPosition();
  }
);
</script>

<template>
  <Listbox as="div" v-model="selected" :multiple="multiple" v-slot="{ open }">
    <div class="relative">
      <div ref="listboxButton">
        <ListboxButton @click="
          setFocus(!open);
        updateDropdownPosition();
        " :class="{
                      'form-input': button,
                      'flex items-center cursor-pointer disabled:cursor-not-allowed relative w-full  text-left focus:outline-none': true,
                      'justify-end': align === 'right',
                      'justify-start': align === 'left',
                    }">
          <div class="flex items-center space-x-2 truncate" v-if="selected && multiple && selected.length >= 1">
            <template v-for="item in selected">
              <div v-if="item.image"
                :class="`h-5 w-5 rounded-full bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-600`">
                <img :src="item.image" :alt="item.label" class="h-5 w-5 rounded-full" />
              </div>
              <div v-if="item.color" :class="`flex-none h-2 w-2 rounded-full bg-${item.color}-500`" />
              <div class="capitalize truncate">
                <span>
                  {{ i18n ? $t(item.label) : item.label }}
                </span>
                <span>
                  {{ selected.length === 1 ? "" : "," }}
                </span>
              </div>
            </template>
          </div>

          <div class="flex items-center space-x-2 truncate font-normal" v-else-if="selected && !multiple">
            <img v-if="selected.image" :src="selected.image" :alt="selected.label" class="h-5 w-5 rounded-full" />

            <div v-if="selected.color" :class="`h-2 w-2 rounded-full bg-${selected.color}-500`" />
            <component v-if="selected.icon" :is="selected.icon" class="h-4 w-4 text-zinc-600 dark:text-white" />

            <div class="capitalize">
              {{ i18n ? $t(selected.label) : selected.label }}
            </div>
          </div>

          <div class="block truncate" v-else>{{ placeholder }}</div>

          <div :class="{
            'pointer-events-none flex items-center': true,
            'text-zinc-400  ml-auto': button,
          }">
            <IconChevronUp v-if="open" class="w-[18px] h-[18px] stroke-2" aria-hidden="true" weight="duotone" />
            <IconChevronDown v-else class="w-[18px] h-[18px] stroke-2" aria-hidden="true" weight="duotone" />
          </div>
        </ListboxButton>
      </div>
      <teleport to="body">
        <transition leave-active-class="transition ease-in duration-100" leave-from-class="opacity-100"
          leave-to-class="opacity-0">
          <ListboxOptions
            class="absolute z-50 mt-1 w-full rounded-md bg-white dark:bg-zinc-900 shadow-2xl border border-zinc-200 dark:border-zinc-600 focus:outline-none text-sm"
            :style="dropdownPosition">
            <div v-if="search" class="relative border-b border-zinc-200 dark:border-zinc-600 rounded-t overflow-hidden">
              <div class="relative px-1 py-1">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                  <IconSearch weight="duotone" class="size-4 text-zinc-400 dark:text-zinc-400 stroke-2" />
                </div>
                <input v-model="query" type="text" ref="input" :placeholder="placeholder"
                  class="w-full pl-10 py-1.5 text-sm outline-none placeholder:text-zinc-500 dark:text-zinc-300 focus:ring-0 border-0 bg-white dark:bg-zinc-900" />
              </div>
            </div>
            <div class="max-h-48 overflow-y-auto">
              <div class="p-1 space-y-0.5">
                <template v-if="filtered.length > 0">
                  <ListboxOption as="template" v-for="option in filtered" :key="option.id" :value="option"
                    v-slot="{ active, selected }" @click="query = ''">
                    <li :class="[
                      active || selected
                        ? 'bg-zinc-100 dark:bg-zinc-800 '
                        : '',
                      'text-sm text-zinc-900 dark:text-zinc-400 relative cursor-pointer select-none py-1.5 px-3 rounded-md flex items-center justify-between',
                    ]">
                      <div :class="[
                        selected
                          ? ' text-zinc-900 dark:text-white'
                          : ' ',
                        'flex items-center space-x-2 truncate font-normal',
                      ]">
                        <img v-if="option.image" :src="option.image" :alt="option.label" class="h-5 w-5 rounded-full" />

                        <div v-if="option.color" :class="`flex-none h-2 w-2 rounded-full bg-${option.color}-400`" />

                        <component v-if="option.icon" :is="option.icon"
                          class="flex-none h-4 w-4 text-zinc-600 dark:text-white" />

                        <div class="capitalize">
                          {{
                            i18n
                              ? $t(option.label)
                              : option.label
                          }}
                        </div>
                      </div>

                      <IconCheck v-if="selected || active" weight="bold"
                        class="h-4 w-4 text-zinc-900 dark:text-green-500 stroke-2" />
                    </li>
                  </ListboxOption>
                </template>

                <template v-else>
                  <li class="text-sm text-zinc-900 dark:text-zinc-400 py-3 px-3">
                    No results found
                  </li>
                </template>
              </div>
            </div>
          </ListboxOptions>
        </transition>
      </teleport>
    </div>
  </Listbox>
</template>