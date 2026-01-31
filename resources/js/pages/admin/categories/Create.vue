<script setup lang="ts">

import { Head, usePage, useForm, router } from '@inertiajs/vue3';
import { computed } from 'vue';
import Form from '@/components/admin/pages/Form.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';

import type { PageForm } from '@/components/admin/pages/Form.vue';

interface Props {
    data: PageForm & { id: number };
}


const props = defineProps<Props>();
const page = usePage();
const locale = computed(() => page.props.locale as string);
const dashboardUrl = computed(() => `/${locale.value}/dashboard`);

function handleSubmit(form: ReturnType<typeof useForm<PageForm>>) {
    form.put(`/${locale.value}/admin/pages/${props.data.id}`, {
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
        title: 'Create Category',
        href: dashboardUrl.value,
    },
];
</script>

<template>
    <Head title="Create Category" />

        <AppLayout :breadcrumbs="breadcrumbs">
            <Form :model="props.data" :onSubmit="handleSubmit" submitLabel="Create Category" />
        </AppLayout>
</template>
