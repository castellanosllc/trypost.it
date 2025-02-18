<script setup>
import { useForm, usePage } from "@inertiajs/vue3";
import { ref } from "vue";
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
import PostMediaUpload from "@/Pages/Post/Edit/Upload.vue";


const confirmDeleteModal = ref(null);

const { post, accounts } = defineProps({
  post: Object,
  accounts: Object,
});

const form = useForm({
  content: "",
  scheduled_at: dayjs().format("YYYY-MM-DD HH:mm:ss"),
  status: "scheduled",
  accounts: [],
});
const show = ref(false);

const open = () => {
  form.reset();
  form.clearErrors();
  show.value = true;
};

const close = () => {
  form.reset();
  form.clearErrors();
  show.value = false;
};

defineExpose({
  open,
});

const store = () => {
  console.log('123');
  // form.put(route("posts.store"), {
  //   preserveScroll: true,
  //   preserveState: true,
  //   onSuccess: () => {
  // form.reset();
  // form.clearErrors();
  // show.value = false;
  // },
  // });
};
</script>

<template>
  <ConfirmDeleteModal ref="confirmDeleteModal" description="Are you sure you want to delete this link?" />

  <SlideOver max-width="5xl" :show="true" @close="router.visit(route('posts.index'))">
    <template #title> Edit Post </template>

    <template #content>
      <div class="flex flex-col gap-4">
        <Accordion :is-open="true">
          <template #title> General </template>
          <template #content>
            <div class="col-span-6">
              <Label for="scheduled_at" value="Date and Time" :required="false" class="sr-only" />

              <div class="flex flex-wrap gap-4">
                <div v-for="account in accounts" :key="account.id" :class="{
                  'cursor-pointer': true,
                  'opacity-50': !form.accounts.includes(
                    account.id
                  ),
                }" @click="
                  form.accounts.includes(account.id)
                    ? form.accounts.splice(
                      form.accounts.indexOf(
                        account.id
                      ),
                      1
                    )
                    : form.accounts.push(account.id)
                  ">
                  <Account :account="account" :tooltip="true" />
                </div>
              </div>
              <InputError :message="form.errors.scheduled_at" class="mt-2" />
            </div>
            <div class="col-span-6">
              <Label for="content" value="Content" />
              <Textarea id="content" v-model="form.content" />

              <InputError :message="form.errors.content" class="mt-2" />
            </div>

            <div class="col-span-6">
              <Label for="scheduled_at" value="Date and Time" :required="false" />

              <DatePicker v-model="form.scheduled_at" mode="dateTime" />
              <InputError :message="form.errors.scheduled_at" class="mt-2" />
            </div>
          </template>
        </Accordion>

        <Accordion :is-open="true">
          <template #title> Media </template>
          <template #content>

            <div class="col-span-6">
              <PostMediaUpload :post="post" />
            </div>
          </template>
        </Accordion>
      </div>
    </template>


    <template #footer>
      <Button @click="
        confirmDeleteModal.open(
          {
            url: route(
              'posts.destroy',
              {
                id: post.id,
              }
            ),
          }
        )
        " :class="{
          'opacity-25': form.processing,
          'btn-primary': true,
        }">
        Delete Post
      </Button>

      <Button @click="store" :class="{
        'opacity-25': form.processing,
        'btn-primary': true,
      }">
        Save as Draft
      </Button>

      <Button @click="store" :class="{
        'opacity-25': form.processing,
        'btn-primary': true,
      }">
        Add to Calendar
      </Button>
    </template>
  </SlideOver>
</template>
