<script setup>
import { reactive, ref } from "vue";
import { Link } from "@inertiajs/vue3";
import Layout from "@/Layouts/Master.vue";
import date from "@/date";
import Account from "@/Components/Account.vue";
import Provider from "@/Components/Provider.vue";

const { accounts } = defineProps({
  accounts: Object,
});

const providers = [
  {
    id: "twitter",
    name: "X",
    href: route("accounts.twitter.connect"),
  },
  {
    id: "linkedin",
    name: "LinkedIn",
    href: route("accounts.linkedin.connect"),
  },
  {
    id: "linkedin-page",
    name: "LinkedIn Page",
    href: route("accounts.linkedin-page.connect"),
  },
  {
    id: "tiktok",
    name: "TikTok",
    href: route("accounts.tiktok.connect"),
  },
];
</script>

<template>
  <Layout>
    <template #header> </template>
    <div class="space-y-8">
      <div>
        <div class="text-2xl font-bold">Connected Accounts</div>
        <div class="flex flex-wrap gap-4">
          <Account v-for="account in accounts" :key="account.id" :account="account" :tooltip="true" size="large" />
        </div>
      </div>

      <div>
        <div class="text-2xl font-bold mb-4">Connect new account</div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
          <Link v-for="provider in providers" :key="provider.name" :href="provider.href"
            class="bg-zinc-100 dark:bg-zinc-800 p-4 rounded-lg flex  flex-col justify-center items-center gap-2">
          <Provider :provider="provider" />
          <div class="font-medium">
            {{ provider.name }}
          </div>
          </Link>
        </div>
      </div>
    </div>
  </Layout>
</template>