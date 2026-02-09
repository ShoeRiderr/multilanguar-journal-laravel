<script setup lang="ts">
import { Head, usePage, router, Link } from '@inertiajs/vue3';
import { computed } from 'vue';

import Pagination from '@/shared/Pagination.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { type Language, PaginationMeta } from '@/types/index';

interface Props {
    language_list: {
        data: Language[];
        meta: PaginationMeta;
    };
}

const props = defineProps<Props>();
const page = usePage();
const locale = computed(() => page.props.locale as string);
const dashboardUrl = computed(() => `/${locale.value}/dashboard`);

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Languages',
        href: dashboardUrl.value,
    },
];

const deleteLanguage = (id: number) => {
    if (!confirm('Delete this language?')) return;
    router.delete(`/${locale.value}/admin/languages/${id}`);
};
</script>

<template>
    <Head title="Languages" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-2">
            <div class="flex items-center justify-between">
                <h1 class="text-xl font-semibold">Languages</h1>
                <Link
                    class="inline-flex items-center rounded-md bg-primary px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary/90"
                    :href="`/${locale}/admin/languages/create`"
                >
                    Create Language
                </Link>
            </div>
    
            <div class="mt-6 overflow-x-auto rounded-lg border border-slate-200 dark:border-slate-700">
                <table class="min-w-full divide-y divide-slate-200 text-sm dark:divide-slate-700">
                    <thead class="bg-slate-50 text-left text-xs font-semibold uppercase text-slate-600 dark:bg-slate-800 dark:text-slate-300">
                        <tr>
                            <th class="px-4 py-3">Code</th>
                            <th class="px-4 py-3">Name</th>
                            <th class="px-4 py-3">Native name</th>
                            <th class="px-4 py-3">Active</th>
                            <th class="px-4 py-3">Default</th>
                            <th class="px-4 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        <tr v-for="language in props.language_list.data" :key="language.id">
                            <td class="px-4 py-3 font-medium text-slate-700 dark:text-slate-200">
                                {{ language.code }}
                            </td>
                            <td class="px-4 py-3 text-slate-600 dark:text-slate-300">{{ language.name }}</td>
                            <td class="px-4 py-3 text-slate-600 dark:text-slate-300">{{ language.native_name }}</td>
                            <td class="px-4 py-3 text-slate-600 dark:text-slate-300">
                                {{ language.is_active ? 'Yes' : 'No' }}
                            </td>
                            <td class="px-4 py-3 text-slate-600 dark:text-slate-300">
                                {{ language.is_default ? 'Yes' : 'No' }}
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex items-center justify-end gap-3">
                                    <Link
                                        class="text-sm font-semibold text-primary hover:underline"
                                        :href="`/${locale}/admin/languages/${language.id}/edit`"
                                    >
                                        Edit
                                    </Link>
                                    <button
                                        type="button"
                                        class="text-sm font-semibold text-red-600 hover:underline"
                                        @click="deleteLanguage(language.id)"
                                    >
                                        Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
    
            <Pagination class="mt-6" :meta="props.language_list.meta" />
        </div>
    </AppLayout>
</template>
