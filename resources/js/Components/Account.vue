<script setup>
const { account } = defineProps({
    account: Object,
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
            :src="account.photo"
            :alt="account.username"
        />

        <span
            class="absolute -bottom-1 -right-1 block size-5 rounded-full bg-green-400 ring-2 ring-white"
        >
            <img
                :src="logo(account.platform)"
                :alt="account.platform"
                class="rounded-full"
            />
        </span>
    </span>
</template>
