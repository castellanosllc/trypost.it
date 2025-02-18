<script setup>
import { reactive, ref } from "vue";
import { router } from "@inertiajs/vue3";
import Layout from "@/Layouts/Master.vue";
import date from "@/date";
import Calendar from "./Calendar.vue";
import Button from "@/Components/Button.vue";
import Edit from "../Edit/Index.vue";

const { post } = defineProps({
  post: {
    type: Object,
    default: null,
  },
});

</script>

<template>
  <Edit v-if="post" :post="post" />
  <Layout>
    <template #header>
      <div class="flex justify-between items-center">
        <h2 class="text-xl font-semibold">Posts</h2>
        <Button class="btn btn-primary" :href="route('posts.store')" method="post">
          Create Post
        </Button>
      </div>
    </template>
    <Calendar />
  </Layout>
</template>
