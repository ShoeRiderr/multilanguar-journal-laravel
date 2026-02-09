<script setup lang="ts">
import { Head, usePage, useForm, router } from '@inertiajs/vue3';
import { computed } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, type Category } from '@/types';
import Form, { type CategoryForm } from '@/components/admin/categories/Form.vue';
import { useTrans } from '@/composables/trans';

interface Props {
    categories: {
        data: Category[];
    };
}

const props = defineProps<Props>();
const page = usePage();
const locale = computed(() => page.props.locale as string);
const dashboardUrl = computed(() => `/${locale.value}/dashboard`);

function handleSubmit(form: ReturnType<typeof useForm<CategoryForm>>) {
    form.post(`/${locale.value}/admin/categories`, {
        onSuccess: () => {
            router.visit(`/${locale.value}/admin/categories`);
        },
    });
}

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: useTrans('admin.create_category'),
        href: dashboardUrl.value,
    },
];
</script>

<template>
    <Head :title="useTrans('admin.create_category')" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <Form
            class="p-2"
            :categories="props.categories.data"
            :onSubmit="handleSubmit"
            :submitLabel="useTrans('admin.categories.create')"
        />
    </AppLayout>
</template>
