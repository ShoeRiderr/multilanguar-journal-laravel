
<script lang="ts" setup>
import { Head, useForm, router, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import Form, { type PageForm } from '@/components/admin/pages/Form.vue';
import { type BreadcrumbItem } from '@/types';

const page = usePage();
const locale = computed(() => page.props.locale as string);
const dashboardUrl = computed(() => `/${locale.value}/dashboard`);

function handleSubmit(form: ReturnType<typeof useForm<PageForm>>) {
    form.post(`/${locale.value}/admin/pages`, {
        onSuccess: () => {
            router.visit(`/${locale.value}/admin/pages`);
        },
    });
}

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Create Page',
        href: dashboardUrl.value,
    },
];
</script>


<template>
    <Head title="Create Page" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <Form
            class="p-2"
            :onSubmit="handleSubmit"
            submitLabel="Create Page"
        />
    </AppLayout>
</template>

