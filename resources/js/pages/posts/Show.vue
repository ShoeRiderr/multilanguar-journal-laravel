<script setup lang="ts">
import { Head, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';

const page = usePage();
const locale = computed(() => page.props.locale as string);
const dashboardUrl = computed(() => `/${locale.value}/dashboard`);

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Edit Post',
        href: dashboardUrl.value,
    },
];

</script>

<template>
    <Head title="Edit Post" />

    <AppLayout :breadcrumbs="breadcrumbs">
        sdsdf
    </AppLayout>
</template>
