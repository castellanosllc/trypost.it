<script setup>
import { useForm, usePage } from "@inertiajs/vue3";
import { ref } from "vue";

import Accordion from "@/Components/Accordion.vue";
import SlideOver from "@/Components/SlideOver.vue";
import Button from "@/Components/Button.vue";
import Input from "@/Components/Input.vue";
import InputError from "@/Components/InputError.vue";
import Label from "@/Components/Label.vue";
import Dropdown from "@/Components/Dropdown.vue";
import Textarea from "@/Components/Textarea.vue";
import DatePicker from "@/Components/DatePicker.vue";
import dayjs from "@/dayjs";
import Account from "@/Components/Account.vue";
const accounts = usePage().props.accounts;

const form = useForm({
    content: "",
    scheduled_at: dayjs().format("YYYY-MM-DD HH:mm:ss"),
    status: "scheduled",
    accounts: [],
});
const show = ref(false);

const open = () => {
    form.reset();
    form.clearErrors();
    show.value = true;
};

const close = () => {
    form.reset();
    form.clearErrors();
    show.value = false;
};

defineExpose({
    open,
});

const store = () => {
    form.post(route("posts.store"), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            form.clearErrors();
            show.value = false;
        },
    });
};
</script>

<template>
    <SlideOver max-width="2xl" :show="show" @close="close()">
        <template #title> New Link </template>

        <template #content>
            <div class="flex flex-col gap-4">
                <Accordion :is-open="true">
                    <template #title> General </template>
                    <template #content>
                        <div class="col-span-6">
                            <Label
                                for="scheduled_at"
                                value="Date and Time"
                                :required="false"
                                class="sr-only"
                            />

                            <div class="flex flex-wrap gap-4">
                                <div
                                    v-for="account in accounts"
                                    :key="account.id"
                                    :class="{
                                        'cursor-pointer': true,
                                        'opacity-50': !form.accounts.includes(
                                            account.id
                                        ),
                                    }"
                                    @click="
                                        form.accounts.includes(account.id)
                                            ? form.accounts.splice(
                                                  form.accounts.indexOf(
                                                      account.id
                                                  ),
                                                  1
                                              )
                                            : form.accounts.push(account.id)
                                    "
                                >
                                    <Account :account="account" />
                                </div>
                            </div>
                            <InputError
                                :message="form.errors.scheduled_at"
                                class="mt-2"
                            />
                        </div>
                        <div class="col-span-6">
                            <Label for="content" value="Content" />
                            <Textarea id="content" v-model="form.content" />

                            <InputError
                                :message="form.errors.content"
                                class="mt-2"
                            />
                        </div>

                        <div class="col-span-6">
                            <Label
                                for="scheduled_at"
                                value="Date and Time"
                                :required="false"
                            />

                            <DatePicker
                                v-model="form.scheduled_at"
                                mode="dateTime"
                            />
                            <InputError
                                :message="form.errors.scheduled_at"
                                class="mt-2"
                            />
                        </div>
                    </template>
                </Accordion>
            </div>
        </template>

        <template #footer>
            <Button
                @click="store"
                :class="{
                    'opacity-25': form.processing,
                    'btn-primary': true,
                }"
                :disabled="form.processing"
            >
                Generate Link
            </Button>
        </template>
    </SlideOver>
</template>
