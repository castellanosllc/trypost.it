<script setup>
import { reactive, ref } from "vue";
import { router } from "@inertiajs/vue3";
import Layout from "@/Layouts/Master.vue";
import date from "@/date";
import Calendar from "./Calendar.vue";
import Create from "../Create.vue";
import Button from "@/Components/Button.vue";

const createModal = ref(null);
</script>

<template>
    <Create ref="createModal" />
    <Layout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold">Posts</h2>
                <Button class="btn btn-primary" @click="createModal.open()"
                    >Create Post</Button
                >
            </div>
        </template>
        <Calendar />
    </Layout>
</template>
