import { computed } from "vue";
import { usePage } from "@inertiajs/vue3";
import posthog from "posthog-js";


export function usePosthog() {
    // Get the authenticated user from Inertia page props
    const user = computed(() => usePage().props.auth?.user);

    const posthogInstance = posthog.init(import.meta.env.VITE_POSTHOG_API_KEY, {
        api_host: import.meta.env.VITE_POSTHOG_HOST,
        person_profiles: "always",
    });

    // Identify user when available
    if (user.value) {
        posthog.identify(user.value.id, {
            email: user.value.email,
            name: user.value?.name,
            workspace_name: user.value.current_workspace?.name,
            workspace_plan_name: user.value.current_workspace?.plan?.name,
            workspace_plan_level: user.value.current_workspace?.plan?.access_level,
            workspace_plan_price: user.value.current_workspace?.plan?.price,
            workspace_plan_max_accounts: user.value.current_workspace?.plan?.max_accounts,
        });
    }

    return posthogInstance;
}
