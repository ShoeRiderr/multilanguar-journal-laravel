<script setup lang="ts">
import { Head, usePage, useForm, router } from '@inertiajs/vue3';
import { computed } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, type Category, type Post } from '@/types';
import Form, { type PostForm } from '@/components/admin/posts/Form.vue';
import { useTrans } from '@/composables/trans';

interface Props {
    post: Post;
    categories: {
        data: Category[];
    };
}

const props = defineProps<Props>();
const page = usePage();
const locale = computed(() => page.props.locale as string);
const dashboardUrl = computed(() => `/${locale.value}/dashboard`);

function handleSubmit(form: ReturnType<typeof useForm<PostForm>>) {
    form.put(`/${locale.value}/admin/posts/${props.post.id}`, {
        onSuccess: () => {
            router.visit(`/${locale.value}/admin/posts`);
        },
        forceFormData: true,
    });
}

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: useTrans('admin.posts.edit_post'),
        href: dashboardUrl.value,
    },
];
</script>

<template>
    <Head :title="useTrans('admin.posts.edit_post')" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <Form
            class="p-2"
            :model="props.post"
            :categories="props.categories.data"
            :onSubmit="handleSubmit"
            :submitLabel="useTrans('admin.posts.update')"
        />
    </AppLayout>
</template>
