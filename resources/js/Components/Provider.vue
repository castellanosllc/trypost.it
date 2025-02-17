<script setup>
const { provider } = defineProps({
    provider: Object,
});

const logo = (platform) => {
    switch (platform) {
        case "twitter":
            return "/images/accounts/x.png";
        case "linkedin":
            return "/images/accounts/linkedin.png";
        case "linkedin-page":
            return "/images/accounts/linkedin.png";
    }
};
</script>

<template>
    <span class="relative inline-block">
        <img
            class="size-10 rounded-full"
            :src="logo(provider.id)"
            :alt="provider.id"
        />
    </span>
</template>
