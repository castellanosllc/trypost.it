<script setup>
import { useForm, usePage, router } from "@inertiajs/vue3";
import { ref, reactive } from "vue";
import Layout from "@/Layouts/Master.vue";
import Accordion from "@/Components/Accordion.vue";
import SlideOver from "@/Components/SlideOver.vue";
import Button from "@/Components/Button.vue";
import Input from "@/Components/Input.vue";
import InputError from "@/Components/InputError.vue";
import Label from "@/Components/Label.vue";
import Dropdown from "@/Components/Dropdown.vue";
import Textarea from "@/Components/Textarea.vue";
import DatePicker from "@/Components/DatePicker.vue";
import ConfirmDeleteModal from "@/Components/ConfirmDeleteModal.vue";
import Modal from "./Partial/Modal.vue";
import dayjs from "@/dayjs";
import Account from "@/Components/Account.vue";

import TwitterForm from "./Partial/Twitter.vue";
import TiktokForm from "./Partial/Tiktok.vue";
import LinkedinForm from "./Partial/Linkedin.vue";
import LinkedinPageForm from "./Partial/LinkedinPage.vue";

import GeneralForm from "./Partial/General.vue";
import MediaForm from "./Partial/Media.vue";

const confirmDeleteModal = ref(null);

const emit = defineEmits(['posts:refresh']);

const { post } = defineProps({
  post: {
    type: Object,
    default: null,
  },
});

const form = useForm({
  ...post,
  scheduled_at: dayjs(post.scheduled_at).format("YYYY-MM-DD HH:mm:ss"),
});

const saveDraft = () => {
  form.status = 'draft';
  update();
}

const addToCalendar = () => {
  form.status = 'scheduled';
  update();
}

const update = () => {
  form.put(route("posts.update", { id: post.id }), {
    preserveScroll: true,
    onSuccess: () => {
      close();
    }
  });
};

const close = () => {
  router.visit(route('posts.index'), {
    preserveScroll: true
  })
}
</script>

<template>
  <ConfirmDeleteModal ref="confirmDeleteModal" @deleted="close" description="Are you sure you want to delete this post?"
    :preserveState="false" />

  <Modal max-width="3xl" :show="true" @close="close">
    <template #title>
      {{ form.status === 'scheduled' ? 'Schedule Post' : 'Draft Post' }}
    </template>
    <template #content>
      <div class="border-b border-zinc-200 dark:border-zinc-800 pb-4">
        <GeneralForm :form="form" />
      </div>
      <div class="flex flex-col space-y-4 divide-y divide-zinc-200 dark:divide-zinc-800">
        <div v-for="postContent in form.post_contents" :key="postContent.id" class="py-4">
          <TwitterForm v-if="postContent.account.platform === 'twitter'" :post-content="postContent"
            :account="postContent.account" />
          <TiktokForm v-if="postContent.account.platform === 'tiktok'" :post-content="postContent"
            :account="postContent.account" />
          <LinkedinForm v-if="postContent.account.platform === 'linkedin'" :post-content="postContent"
            :account="postContent.account" />
          <LinkedinPageForm v-if="postContent.account.platform === 'linkedin-page'" :post-content="postContent"
            :account="postContent.account" />
        </div>
      </div>
    </template>
    <template #footer>
      <Button @click="
        confirmDeleteModal.open({
          url: route('posts.destroy', {
            id: post.id,
          }),
        })
        " :class="{
          'btn-danger': true,
        }">
        Delete Post
      </Button>

      <Button @click="saveDraft" :class="{
        'opacity-25': form.processing,
        'btn-primary': true,
      }">
        Save as Draft
      </Button>

      <Button @click="addToCalendar" :class="{
        'opacity-25': form.processing,
        'btn-primary': true,
      }">
        Add to Calendar
      </Button>
    </template>
  </Modal>
</template>