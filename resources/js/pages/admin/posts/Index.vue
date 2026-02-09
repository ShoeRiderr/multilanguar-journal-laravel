<script setup lang="ts">
import { Head, usePage, router, Link } from '@inertiajs/vue3';
import { computed } from 'vue';

import Pagination from '@/shared/Pagination.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { type Language, type Post, PaginationMeta } from '@/types/index';

interface Props {
    posts: {
        data: Post[];
        meta: PaginationMeta;
    };
}

const props = defineProps<Props>();
const page = usePage();
const locale = computed(() => page.props.locale as string);
const languages = computed(() => (page.props.languages as Language[]) || []);
const dashboardUrl = computed(() => `/${locale.value}/dashboard`);

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Posts',
        href: dashboardUrl.value,
    },
];

const deletePost = (id: number) => {
    if (!confirm('Delete this post?')) return;
    router.delete(`/${locale.value}/admin/posts/${id}`);
};
</script>

<template>
    <Head title="Posts" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-2">
            <div class="flex items-center justify-between">
                <h1 class="text-xl font-semibold">Posts</h1>
                <Link
                    class="inline-flex items-center rounded-md bg-primary px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary/90"
                    :href="`/${locale}/admin/posts/create`"
                >
                    Create Post
                </Link>
            </div>
    
            <div class="mt-6 overflow-x-auto rounded-lg border border-slate-200 dark:border-slate-700">
                <table class="min-w-full divide-y divide-slate-200 text-sm dark:divide-slate-700">
                    <thead class="bg-slate-50 text-left text-xs font-semibold uppercase text-slate-600 dark:bg-slate-800 dark:text-slate-300">
                        <tr>
                            <th class="px-4 py-3">Title</th>
                            <th class="px-4 py-3">Language</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Published</th>
                            <th class="px-4 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        <tr v-for="post in props.posts.data" :key="post.id">
                            <td class="px-4 py-3 font-medium text-slate-700 dark:text-slate-200">
                                {{ post.title }}
                            </td>
                            <td class="px-4 py-3 text-slate-600 dark:text-slate-300">
                                {{ languages.find((lang) => lang.id === post.language_id)?.name || post.language_id }}
                            </td>
                            <td class="px-4 py-3 text-slate-600 dark:text-slate-300 capitalize">
                                {{ post.status }}
                            </td>
                            <td class="px-4 py-3 text-slate-600 dark:text-slate-300">
                                {{ post.published_at || '-' }}
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex items-center justify-end gap-3">
                                    <Link
                                        class="text-sm font-semibold text-primary hover:underline"
                                        :href="`/${locale}/admin/posts/${post.id}/edit`"
                                    >
                                        Edit
                                    </Link>
                                    <button
                                        type="button"
                                        class="text-sm font-semibold text-red-600 hover:underline"
                                        @click="deletePost(post.id)"
                                    >
                                        Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
    
            <Pagination class="mt-6" :meta="props.posts.meta" />
        </div>
    </AppLayout>
</template>
