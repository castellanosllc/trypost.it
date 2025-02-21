<script setup>
import { useForm, usePage, router } from "@inertiajs/vue3";
import { ref, reactive } from "vue";
import Layout from "@/Layouts/Master.vue";
import Accordion from "@/Components/Accordion.vue";
import InputError from "@/Components/InputError.vue";
import Label from "@/Components/Label.vue";
import Textarea from "@/Components/Textarea.vue";
import DatePicker from "@/Components/DatePicker.vue";
import dayjs from "@/dayjs";
import Account from "@/Components/Account.vue";
import PostMediaUpload from "./Media.vue";

const { accounts } = usePage().props;

const { form } = defineProps({
  form: Object,
})

const deletePostContent = (id) => {
  router.visit(route('post-contents.destroy', { id: form.id, postContentId: id }), {
    method: 'delete',
    preserveScroll: true,
    preserveState: false,
  });
}

const createPostContent = (account) => {
  router.visit(route('post-contents.store', { id: form.id }), {
    method: 'post',
    data: {
      account_id: account.id,
    },
    preserveScroll: true,
    preserveState: false,
  });
}

const togglePostContent = (account) => {

  const postContent = form.post_contents.find(pc => pc.account_id === account.id);

  if (postContent) {
    deletePostContent(postContent.id);
  } else {
    createPostContent(account);
  }
}
</script>

<template>

  <div class="col-span-6">
    <Label for="scheduled_at" value="Date and Time" :required="false" class="sr-only" />

    <div class="flex flex-wrap gap-4">
      <div v-for="account in accounts" :key="account.id" :class="{
        'cursor-pointer': true,
        'opacity-50': !form.post_contents.some(pc => pc.account_id === account.id),
      }" @click="togglePostContent(account)">
        <Account :account="account" :tooltip="true" size="large" />
      </div>
    </div>
    <InputError :message="form.errors.post_contents" class="mt-2" />
  </div>
  <!--

      <div class="col-span-6">
        <Label for="scheduled_at" value="Date and Time" :required="false" />
        <DatePicker v-model="form.scheduled_at" mode="dateTime" />
        <InputError :message="form.errors.scheduled_at" class="mt-2" />
      </div> -->
</template>