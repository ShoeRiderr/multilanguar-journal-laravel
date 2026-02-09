<script setup lang="ts">
import { Head, usePage, useForm, router } from '@inertiajs/vue3';
import { computed } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, type Category } from '@/types';
import Form, { type PostForm } from '@/components/admin/posts/Form.vue';

interface Props {
    categories: {
        data: Category[];
    };
}

const props = defineProps<Props>();
const page = usePage();
const locale = computed(() => page.props.locale as string);
const dashboardUrl = computed(() => `/${locale.value}/dashboard`);

function handleSubmit(form: ReturnType<typeof useForm<PostForm>>) {
    form.post(`/${locale.value}/admin/posts`, {
        onSuccess: () => {
            router.visit(`/${locale.value}/admin/posts`);
        },
        forceFormData: true,
    });
}

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Create Post',
        href: dashboardUrl.value,
    },
];
</script>

<template>
    <Head title="Create Post" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <Form
            class="p-2"
            :categories="props.categories.data"
            :onSubmit="handleSubmit"
            submitLabel="Create Post"
        />
    </AppLayout>
</template>
