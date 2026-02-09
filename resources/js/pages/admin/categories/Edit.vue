<script setup lang="ts">
import { Head, usePage, useForm, router } from '@inertiajs/vue3';
import { computed } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, type Category } from '@/types';
import Form, { type CategoryForm } from '@/components/admin/categories/Form.vue';
import type { CategoryTranslationForm } from '@/components/admin/categories/Form.vue';

interface Props {
    category: {
        id: number;
        parent_id: number | null;
        translations: CategoryTranslationForm[];
    };
    categories: {
        data: Category[];
    };
}

const props = defineProps<Props>();
const page = usePage();
const locale = computed(() => page.props.locale as string);
const dashboardUrl = computed(() => `/${locale.value}/dashboard`);

function handleSubmit(form: ReturnType<typeof useForm<CategoryForm>>) {
    form.put(`/${locale.value}/admin/categories/${props.category.id}`, {
        onSuccess: () => {
            router.visit(`/${locale.value}/admin/categories`);
        },
    });
}

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Edit Category',
        href: dashboardUrl.value,
    },
];
</script>

<template>
    <Head title="Edit Category" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <Form
            class="p-2"
            :model="props.category"
            :categories="props.categories.data"
            :onSubmit="handleSubmit"
            submitLabel="Update Category"
        />
    </AppLayout>
</template>
