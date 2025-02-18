<script setup>
import { ref, computed } from 'vue'
import { RadioGroup, RadioGroupOption } from '@headlessui/vue'
import { IconCheck } from '@tabler/icons-vue'
import { Head} from "@inertiajs/vue3";
import Banner from "@/Components/Banner.vue";

const frequencies = [
  { value: 'monthly', label: 'Monthly'},
  { value: 'yearly', label: 'Yearly'},
]

const frequency = ref(frequencies[0]);

const { plans } = defineProps({
  plans: Object
})

const tiers = computed(() => {

    const p =  plans.filter(plan => frequency.value.value === 'monthly' ? plan.is_monthly : !plan.is_monthly);

    return p.map(plan => ({
        name: plan.name,
        id: `plan-${plan.internal_id}`,
        href: route('setting.billing.checkout', { id: plan.id }),
        description: 'Dedicated support and infrastructure for your company.',
        price: `$${plan.is_monthly ? plan.price : (Math.round(plan.price / 12))}`,
        features: [
            `${plan.max_accounts} social accounts`,
            'All social networks',
            'Unlimited posts',
            'Scheduled posts',
            'AI writing assistant (soon)',
            'Chat support',
        ],
        mostPopular: plan.name === 'Growth'
    }));
})


import { useDarkTheme } from "@/theme";
const { isDarkTheme } = useDarkTheme();
</script>

<template>
    <Head title="Start your free trial" />

    <Banner />

    <div class="bg-white py-24 sm:py-32">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
        <div class="mx-auto max-w-4xl text-center">
            <p class="mt-2 text-balance text-5xl font-semibold tracking-tight text-zinc-900 sm:text-6xl">
                Start your free trial
            </p>
        </div>
        <p class="mx-auto mt-6 max-w-2xl text-pretty text-center text-base text-zinc-600 sm:text-lg">Choose an affordable plan that’s packed with the best features for engaging your audience, creating customer loyalty, and driving sales.</p>
        <div class="mt-8 flex justify-center">
            <fieldset aria-label="Payment frequency">
            <RadioGroup v-model="frequency" class="grid grid-cols-2 gap-x-1 rounded-full p-1 text-center text-xs/5 font-semibold ring-1 ring-inset ring-zinc-200">
                <RadioGroupOption as="template" v-for="option in frequencies" :key="option.value" :value="option" v-slot="{ checked }">
                <div :class="[checked ? 'bg-indigo-600 text-white' : 'text-zinc-500', 'cursor-pointer rounded-full px-2.5 py-1']">{{ option.label }}</div>
                </RadioGroupOption>
            </RadioGroup>
            </fieldset>
        </div>
        <div class="isolate mx-auto mt-10 grid max-w-md grid-cols-1 gap-8 md:max-w-2xl md:grid-cols-2 lg:max-w-4xl xl:mx-0 xl:max-w-none xl:grid-cols-4">
            <div v-for="tier in tiers" :key="tier.id" :class="[tier.mostPopular ? 'ring-2 ring-indigo-600' : 'ring-1 ring-zinc-200', 'rounded-3xl p-8']">
                <h3 :id="tier.id" :class="[tier.mostPopular ? 'text-indigo-600' : 'text-zinc-900', 'text-lg/8 font-semibold']">{{ tier.name }}</h3>
                <p class="mt-4 text-sm/6 text-zinc-600">{{ tier.description }}</p>
                <div class="mt-6 flex flex-col gap-x-1">
                    <div class="flex items-baseline">
                        <div class="text-4xl font-semibold tracking-tight text-zinc-900">{{ tier.price }}</div>
                        <div class="text-sm font-normal text-zinc-600">/month</div>
                    </div>
                    <div v-if="frequency.value === 'yearly'" class="mt-2 text-sm font-normal text-zinc-600">Billing yearly</div>
                </div>

                <a :href="tier.href" :class="{
                    'btn w-full mt-6': true,
                    'btn-primary': tier.mostPopular,
                    'btn-secondary': !tier.mostPopular,
                }">
                    Start 7-day trial
                </a>

                <ul role="list" class="mt-8 space-y-2 text-sm/6 text-zinc-600">
                    <li v-for="feature in tier.features" :key="feature" class="flex gap-x-3">
                    <IconCheck class="h-6 w-5 flex-none text-indigo-600" aria-hidden="true" />
                    {{ feature }}
                    </li>
                </ul>
            </div>
        </div>
        </div>
    </div>
</template>
