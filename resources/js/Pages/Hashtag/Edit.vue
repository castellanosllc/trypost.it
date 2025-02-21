<script setup>
import { useForm, usePage } from "@inertiajs/vue3";
import { ref } from "vue";

import DialogModal from "@/Components/DialogModal.vue";
import Button from "@/Components/Button.vue";
import Input from "@/Components/Input.vue";
import InputError from "@/Components/InputError.vue";
import Label from "@/Components/Label.vue";
import Textarea from "@/Components/Textarea.vue";

const form = useForm({
  id: "",
  name: "",
  collection: "",
});
const show = ref(false);

const open = (hashtag) => {
  form.reset();

  form.id = hashtag.id;
  form.name = hashtag.name;
  form.collection = hashtag.collection;

  show.value = true;
};

defineExpose({
  open,
});

const update = () => {
  form.put(
    route("setting.hashtags.update", {
      id: form.id,
    }),
    {
      preserveScroll: true,
      onSuccess: () => {
        form.reset();
        show.value = false;
      },
    }
  );
};
</script>

<template>
  <DialogModal max-width="lg" :show="show" @close="show = null">
    <template #title>Edit Hashtag</template>

    <template #content>
      <div class="mt-6 grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
        <div class="sm:col-span-6">
          <Label for="name" value="Name" :required="true" />
          <Input id="name" type="text" v-model="form.name" placeholder="" />
          <InputError :message="form.errors.name" class="mt-2" />
        </div>

        <div class="sm:col-span-6">
          <Label for="collection" value="Collection" :required="true" />
          <Textarea id="collection" v-model="form.collection" placeholder="" />
          <InputError :message="form.errors.collection" class="mt-2" />
        </div>
      </div>
    </template>

    <template #footer>
      <Button @click="update" :class="{
        'opacity-25': form.processing,
        'btn-primary w-full': true,
      }" :disabled="form.processing">
        Update Hashtag
      </Button>
    </template>
  </DialogModal>
</template>