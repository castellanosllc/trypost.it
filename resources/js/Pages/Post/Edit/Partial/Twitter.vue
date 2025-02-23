<script setup>
import { watch } from "vue";
import { useForm } from "@inertiajs/vue3";
import debounce from "@/debounce";

import InputError from "@/Components/InputError.vue";
import Label from "@/Components/Label.vue";
import Textarea from "@/Components/Textarea.vue";
import Dropdown from "@/Components/Dropdown.vue";

import Media from "./Media.vue";
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
  <div class="flex items-center gap-2 mb-6">
    <Account :account="props.account" :tooltip="true" size="medium" />
    <div class="font-medium">
      {{ `@${props.account.username}` }}
    </div>
  </div>
  <div class="flex items-start gap-4">
    <div class="w-5/12 ">
      <Media :postContent="props.postContent" />
    </div>
    <div class="w-7/12 space-y-4 ">
      <div>
        <Label for="type" value="Post Type" :required="true" />
        <Dropdown id="type" :options="[
          {
            id: 'text',
            label: 'Text',
          },
          {
            id: 'image',
            label: 'Image',
          }
        ]" class="w-full" v-model="form.type" />

        <InputError :message="form.errors.type" class="mt-2" />
      </div>

      <div>
        <Textarea id="content" v-model="form.content" :rows="2" :resize="true" :showCounter="true" :maxLength="280" />
        <InputError :message="form.errors.content" class="mt-2" />
      </div>
    </div>
  </div>
</template>