<script setup>
import Account from "@/Components/Account.vue";
const props = defineProps({
  postContent: {
    type: Object,
    required: true,
  },
  account: {
    type: Object,
    required: true,
  },
});
</script>

<template>
  <div class="flex items-center gap-2">
    <Account :account="props.account" :tooltip="true" size="medium" />
    <div class="font-medium">
      {{ `@${props.account.username}` }}
    </div>
  </div>
</template>