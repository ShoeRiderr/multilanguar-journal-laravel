<script setup lang="ts">
import { Head, usePage, router, Link } from '@inertiajs/vue3';
import { computed } from 'vue';

import Pagination from '@/shared/Pagination.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { type Pages, PaginationMeta } from '@/types/index';

interface Props {
    pages: {
        data: Pages[];
        meta: PaginationMeta;
    };
}

const props = defineProps<Props>();
const page = usePage();
const locale = computed(() => page.props.locale as string);
const dashboardUrl = computed(() => `/${locale.value}/dashboard`);

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Pages',
        href: dashboardUrl.value,
    },
];

const deletePage = (id: number) => {
    if (!confirm('Delete this page?')) return;
    router.delete(`/${locale.value}/admin/pages/${id}`);
};
</script>

<template>
    <Head title="Pages" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-2">
            <div class="flex items-center justify-between">
                <h1 class="text-xl font-semibold">Pages</h1>
                <Link
                    class="inline-flex items-center rounded-md bg-primary px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary/90"
                    :href="`/${locale}/admin/pages/create`"
                >
                    Create Page
                </Link>
            </div>
    
            <div class="mt-6 overflow-x-auto rounded-lg border border-slate-200 dark:border-slate-700">
                <table class="min-w-full divide-y divide-slate-200 text-sm dark:divide-slate-700">
                    <thead class="bg-slate-50 text-left text-xs font-semibold uppercase text-slate-600 dark:bg-slate-800 dark:text-slate-300">
                        <tr>
                            <th class="px-4 py-3">Title</th>
                            <th class="px-4 py-3">Slug</th>
                            <th class="px-4 py-3">Active</th>
                            <th class="px-4 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        <tr v-for="pageItem in props.pages.data" :key="pageItem.id">
                            <td class="px-4 py-3 font-medium text-slate-700 dark:text-slate-200">
                                {{ pageItem.title }}
                            </td>
                            <td class="px-4 py-3 text-slate-600 dark:text-slate-300">{{ pageItem.slug }}</td>
                            <td class="px-4 py-3 text-slate-600 dark:text-slate-300">
                                {{ pageItem.is_active ? 'Yes' : 'No' }}
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex items-center justify-end gap-3">
                                    <Link
                                        class="text-sm font-semibold text-primary hover:underline"
                                        :href="`/${locale}/admin/pages/${pageItem.id}/edit`"
                                    >
                                        Edit
                                    </Link>
                                    <button
                                        type="button"
                                        class="text-sm font-semibold text-red-600 hover:underline"
                                        @click="deletePage(pageItem.id)"
                                    >
                                        Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
    
            <Pagination class="mt-6" :meta="props.pages.meta" />
        </div>
    </AppLayout>
</template>
