<script setup>
import { watch } from "vue";
import { useForm } from "@inertiajs/vue3";
import debounce from "@/debounce";

import InputError from "@/Components/InputError.vue";
import Label from "@/Components/Label.vue";
import Textarea from "@/Components/Textarea.vue";
import MediaForm from "./Media.vue";
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

const form = useForm({
  ...props.postContent,
});

const update = debounce(() => {
  form.put(route('post-contents.update', { id: props.postContent.post_id, postContentId: props.postContent.id }), {
    preserveScroll: true,
    preserveState: true,
  });
}, 500);

watch(
  () => form.isDirty,
  () => {
    update();
  },
  {
    deep: true,
  }
);

</script>

<template>
  <div class="flex items-center gap-2">
    <Account :account="props.account" :tooltip="true" size="medium" />
    <div class="font-medium">
      {{ `@${props.account.username}` }}
    </div>
  </div>
  <div class="grid grid-cols-10 gap-4">
    <div class="col-span-4">
      <MediaForm :post-content="props.postContent" />
    </div>
    <div class="col-span-6">
      <Label for="content" value="Content" />
      <Textarea id="content" v-model="form.content" rows="8" />
      <InputError :message="form.errors.content" class="mt-2" />
    </div>
  </div>
</template>