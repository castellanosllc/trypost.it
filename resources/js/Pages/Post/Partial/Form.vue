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
import PostMediaUpload from "./Upload.vue";

const { accounts } = usePage().props;

const { form } = defineProps({
  form: Object,
})
</script>

<template>
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
              <Account :account="account" :tooltip="true" size="large" />
            </div>
          </div>
          <InputError :message="form.errors.accounts" class="mt-2" />
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
          <PostMediaUpload :post="form" />
        </div>
      </template>
    </Accordion>
  </div>
</template>
