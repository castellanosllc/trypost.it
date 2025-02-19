<script setup>
import { computed } from "vue";
const { account, tooltip, size } = defineProps({
  account: {
    type: Object,
    required: true,
  },

  tooltip: {
    type: Boolean,
    default: false,
  },

  size: {
    type: String,
    default: "big",
  },
});

const logo = (platform) => {
  switch (platform) {
    case "twitter":
      return "/images/accounts/x.png";
    case "linkedin":
      return "/images/accounts/linkedin.png";
    case "linkedin-page":
      return "/images/accounts/linkedin.png";
  }
};

const mainClass = computed(() => {
  switch (size) {
    case "large":
      return "size-10";
    case "medium":
      return "size-6";
    case "small":
      return "size-4";
  }
});

const ringClass = computed(() => {
  switch (size) {
    case "large":
      return "size-5";
    case "medium":
      return "size-3";
    case "small":
      return "size-2";
  }
});
</script>

<template>
  <span class="relative inline-block" v-tooltip="tooltip ? account.username : null">
    <img class="rounded-full" :src="account.photo" :alt="account.username" :class="mainClass" />

    <span class="absolute -bottom-1 -right-1 block rounded-full bg-green-400 ring-2 ring-white" :class="ringClass">
      <img :src="logo(account.platform)" :alt="account.platform" class="rounded-full" />
    </span>
  </span>
</template>
