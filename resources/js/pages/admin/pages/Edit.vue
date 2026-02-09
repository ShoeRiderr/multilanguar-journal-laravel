<script setup lang="ts">

import { Head, usePage, useForm, router } from '@inertiajs/vue3';
import { computed } from 'vue';
import Form, { type PageForm, type PageTranslationForm } from '@/components/admin/pages/Form.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';

interface Props {
    page: {
        id: number;
        is_active: boolean;
        translations: PageTranslationForm[];
    };
}

const props = defineProps<Props>();
const page = usePage();
const locale = computed(() => page.props.locale as string);
const dashboardUrl = computed(() => `/${locale.value}/dashboard`);

function handleSubmit(form: ReturnType<typeof useForm<PageForm>>) {
    form.put(`/${locale.value}/admin/pages/${props.page.id}`, {
        onSuccess: () => {
            router.visit(`/${locale.value}/admin/pages`)
        },
        onError: () => {
            // Optionally handle errors
        }
    })
}

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Edit Page',
        href: dashboardUrl.value,
    },
];
</script>

<template>
    <Head title="Edit Page" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <Form
            class="p-2"
            :model="props.page"
            :onSubmit="handleSubmit"
            submitLabel="Update Page"
        />
    </AppLayout>
</template>
