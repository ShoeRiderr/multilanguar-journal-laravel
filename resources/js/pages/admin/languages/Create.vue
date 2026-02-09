<script setup lang="ts">
import { Head, usePage, useForm, router } from '@inertiajs/vue3';
import { computed } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import Form, { type LanguageForm } from '@/components/admin/languages/Form.vue';

const page = usePage();
const locale = computed(() => page.props.locale as string);
const dashboardUrl = computed(() => `/${locale.value}/dashboard`);

function handleSubmit(form: ReturnType<typeof useForm<LanguageForm>>) {
    form.post(`/${locale.value}/admin/languages`, {
        onSuccess: () => {
            router.visit(`/${locale.value}/admin/languages`);
        },
    });
}

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Create Language',
        href: dashboardUrl.value,
    },
];
</script>

<template>
    <Head title="Create Language" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <Form
            class="p-2"
            :onSubmit="handleSubmit"
            submitLabel="Create Language"
        />
    </AppLayout>
</template>
