<script setup>
import { useForm, usePage, router } from "@inertiajs/vue3";
import { ref, reactive, watch } from "vue";
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
import dayjs from "@/dayjs";
import Account from "@/Components/Account.vue";
import Form from "./Partial/Form.vue";

const loading = ref(false);
const show = ref(false);

const { accounts } = usePage().props;

const form = reactive({
  content: '',
  scheduled_at: 'draft',
  status: '',
  accounts: [],
  media: [],

  errors: {},
});

const close = () => {
  show.value = false;
}

const open = (datetime) => {

  // fill form with post data
  form.content = '';
  form.scheduled_at = datetime;
  form.status = 'draft';
  form.accounts = [];
  form.media = [];
  show.value = true;
}

defineExpose({
  open,
  close,
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
  axios.post(route("posts.store"), {
    ...form,
  }).then((response) => {
    close();
  });
};

watch(form, (newVal) => {
  console.log(newVal);
}, { deep: true });
</script>

<template>
  <SlideOver max-width="3xl" :show="show" @close="close">
    <template #title> New Post </template>

    <template #content>
      <Form :form="form" />
    </template>


    <template #footer>
      <Button @click="saveDraft" :class="{
        'opacity-25': loading,
        'btn-primary': true,
      }">
        Save as Draft
      </Button>

      <Button @click="addToCalendar" :class="{
        'opacity-25': loading,
        'btn-primary': true,
      }">
        Add to Calendar
      </Button>
    </template>
  </SlideOver>
</template>
