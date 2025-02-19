<script setup>
import Layout from "@/Layouts/Master.vue";
import Calendar from "./Calendar.vue";
</script>

<template>
  <Layout :overflow="false" :fluid="true">
    <Calendar />
  </Layout>
</template>
