<script setup lang="ts">
import { Head, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

import Pagination from '@/shared/Pagination.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';

import PlaceholderPattern from '@/components/PlaceholderPattern.vue';

interface Category {
    id: number;
    parent_id: number | null;
    name: string | null;
    slug: string | null;
    created_at: string;
    updated_at: string;
}

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

interface PaginationMeta {
    current_page: number;
    from: number;
    last_page: number;
    path: string;
    per_page: number;
    to: number;
    total: number;
}

interface Props {
    categories: {
        data: Category[];
        meta: PaginationMeta;
    };
}

const props = defineProps<Props>();
const page = usePage();
const locale = computed(() => page.props.locale as string);
const dashboardUrl = computed(() => `/${locale.value}/dashboard`);

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Categories',
        href: dashboardUrl.value,
    },
];

// Build pagination links from meta
const paginationLinks = computed(() => {
    const links: PaginationLink[] = [];
    const meta = props.categories.meta;
    const path = meta.path;
    
    // Previous page
    if (meta.current_page > 1) {
        links.push({
            url: `${path}?page=${meta.current_page - 1}`,
            label: '← Previous',
            active: false,
        });
    }
    
    // Page numbers
    for (let i = 1; i <= meta.last_page; i++) {
        links.push({
            url: i === 1 ? path : `${path}?page=${i}`,
            label: i.toString(),
            active: i === meta.current_page,
        });
    }
    
    // Next page
    if (meta.current_page < meta.last_page) {
        links.push({
            url: `${path}?page=${meta.current_page + 1}`,
            label: 'Next →',
            active: false,
        });
    }
    
    return links;
});
</script>

<template>
    <Head title="Categories" />

    <AppLayout :breadcrumbs="breadcrumbs">
    <div>
      <div v-for="category in categories.data" :key="category.id">
        {{ category.name }}
      </div>
      <pagination :links="paginationLinks" />
    </div>

    </AppLayout>
</template>
