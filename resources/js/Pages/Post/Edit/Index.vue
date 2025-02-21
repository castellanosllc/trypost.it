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
import dayjs from "@/dayjs";
import Account from "@/Components/Account.vue";
import GeneralForm from "./Form/General.vue";
import MediaForm from "./Form/Media.vue";

const confirmDeleteModal = ref(null);

const emit = defineEmits(['posts:refresh']);

const { post } = defineProps({
  post: {
    type: Object,
    default: null,
  },
});

const form = useForm({
  content: post.content,
  scheduled_at: dayjs(post.scheduled_at).format("YYYY-MM-DD HH:mm:ss"),
  status: post.status,
  accounts: post.post_stats.map((a) => a.account_id),
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

  <SlideOver max-width="2xl" :show="true" @close="close">
    <template #title> Edit Post </template>
    <template #content>
      <div class="flex flex-col gap-4">
        <GeneralForm :form="form" />
        <MediaForm :post="post" />
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
  </SlideOver>
</template>
