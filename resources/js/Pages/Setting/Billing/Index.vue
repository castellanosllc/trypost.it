<script setup>
import { ref, computed, onMounted, reactive } from "vue";
import { Head, usePage, Link, useForm } from "@inertiajs/vue3";

import {
    PhInfo,
    PhLink,
    PhCursorClick,
    PhTag,
    PhGlobe,
    PhUsers,
} from "@phosphor-icons/vue";

import AppLayout from "@/Layouts/Master.vue";
import Button from "@/Components/Button.vue";

const usage = computed(() => usePage().props.usage);

const accounts = computed(() => {
    return `${usage.value.accounts.remaining} remaining of ${usage.value.accounts.limit}`;
});
</script>

<template>
    <Head title="Pricing - Changelogfy" />

    <AppLayout>
        <div class="space-y-4">
            <div
                class="border border-zinc-200 dark:border-zinc-700 rounded-lg overflow-hidden px-6 py-8 text-zinc-800 dark:text-zinc-300"
            >
                <div class="flex flex-col justify-center space-y-4">
                    <div class="sm:flex sm:items-center">
                        <div class="sm:flex-auto">
                            <h1 class="page-title">Plan and Usage</h1>
                        </div>

                        <Button
                            v-if="usage.billing.has_subscription"
                            :href="route('setting.billing.portal')"
                            class="btn btn-secondary"
                        >
                            Manage Subscription
                        </Button>
                    </div>

                    <div class="flex items-center justify-between">
                        <div>
                            You are currently on the <b>{{ usage.plan.name }}</b
                            >. Current billing cycle:
                            <b>{{ usage.current_billing_cycle_formatted }}</b>
                        </div>
                    </div>
                </div>
            </div>

            <div
                class="mx-auto grid grid-cols-1 gap-px bg-zinc-100 dark:bg-zinc-700 sm:grid-cols-3 border border-zinc-200 dark:border-zinc-700 rounded-lg overflow-hidden"
            >
                <div class="bg-white dark:bg-zinc-800 p-4 space-y-4">
                    <div class="space-y-2">
                        <PhTag class="h-6 w-6 text-zinc-400" />
                        <div class="text-zinc-800 dark:text-zinc-300">Accounts</div>
                        <div
                            class="text-2xl font-semibold text-zinc-800 dark:text-zinc-300"
                        >
                            {{ usage.accounts.used }}
                        </div>
                    </div>
                    <div>
                        <div class="overflow-hidden rounded-full bg-zinc-200">
                            <div
                                class="h-1 rounded-full bg-gradient-to-r from-violet-400 to-violet-600"
                                :style="`width: ${usage.accounts.percent}%`"
                            />
                        </div>
                        <div
                            class="mt-2 text-zinc-800 dark:text-zinc-300 text-sm"
                        >
                            {{ accounts }}
                        </div>
                    </div>
                </div>
            </div>

            <div
                class="border border-zinc-200 dark:border-zinc-700 rounded-lg overflow-hidden p-4"
            >
                <div class="flex items-center w-full space-x-2">
                    <div class="flex-shrink-0">
                        <PhInfo class="h-5 w-5 text-zinc-400" />
                    </div>
                    <div
                        class="flex w-full justify-between items-center space-x-2"
                    >
                        <div
                            class="text-sm font-medium text-zinc-800 dark:text-white"
                        >
                            For higher limits, upgrade to the
                            {{ usage.plan.next_tier.name }} plan.
                        </div>
                        <div>
                            <Button
                                :href="
                                    usage.billing.has_subscription
                                        ? route('setting.billing.portal')
                                        : route('setting.billing.upgrade')
                                "
                                class="btn btn-primary"
                            >
                                Upgrade
                            </Button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
