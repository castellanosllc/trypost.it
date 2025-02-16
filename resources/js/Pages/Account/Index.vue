<script setup>
import { reactive, ref } from "vue";
import { router } from "@inertiajs/vue3";
import Layout from "@/Layouts/Master.vue";
import date from "@/date";

const { accounts } = defineProps({
    accounts: Object,
});
</script>

<template>
    <Layout>
        <template #header> </template>
        <div>Accounts</div>

        <div v-for="account in accounts" :key="account.id">
            <div>
                <img :src="account.photo" alt="Account Photo" />
                <div>
                    <h3>{{ account.username }}</h3>
                </div>
            </div>
        </div>
    </Layout>
</template>
