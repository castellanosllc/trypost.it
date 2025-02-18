<script setup>
import { ref, watch, onMounted } from "vue";
import { Head, Link, router } from "@inertiajs/vue3";
import Button from "@/Components/Button.vue";

import { useDarkTheme } from "@/theme";
const { isDarkTheme } = useDarkTheme();

const { workspaces } = defineProps({
    workspaces: Object,
});

const switchToWorkspace = (workspace) => {
    router.put(
        route("workspaces.update-current"),
        {
            workspace_id: workspace.id,
        },
        {
            preserveState: false,
        }
    );
};
</script>

<template>
    <Head title="Your Workspaces" />

    <div
        class="flex flex-col items-center justify-center min-h-screen bg-zinc-100 dark:bg-zinc-950"
    >
        <div
            class="max-w-lg w-full bg-white dark:bg-zinc-900 rounded-lg px-6 py-6 lg:px-8 border border-zinc-200 dark:border-zinc-700"
        >
            <div
                class="text-zinc-600 dark:text-zinc-300 text-center text-base mb-6"
            >
                Workspaces let you collaborate with your team to manage your
                accounts, posts, and media.
            </div>

            <div class="space-y-4">
                <div
                    v-for="workspace in workspaces"
                    :key="workspace.id"
                    @click="switchToWorkspace(workspace)"
                    class="flex items-center justify-between p-4 border border-zinc-200 dark:border-zinc-700 rounded-lg cursor-pointer hover:bg-zinc-50 dark:hover:bg-zinc-800"
                >
                    <div class="flex items-center space-x-3">
                        <div>
                            <img
                                class="w-9 h-9 flex items-center justify-center rounded-full"
                                :src="workspace.logo_url"
                            />
                        </div>

                        <div
                            class="text-sm text-zinc-800 dark:text-zinc-300 font-semibold"
                        >
                            {{ workspace.name }}
                        </div>
                    </div>
                </div>

                <Button
                    class="btn-primary w-full"
                    :href="route('workspaces.create')"
                >
                    New workspace
                </Button>

                <Button
                    :href="route('posts.index')"
                    class="btn-secondary w-full"
                >
                    Cancel
                </Button>
            </div>
        </div>
    </div>
</template>
