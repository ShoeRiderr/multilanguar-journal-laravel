<script setup lang="ts">
import { Head, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

import Pagination from '@/shared/Pagination.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { type Pages, PaginationLink, PaginationMeta } from '@/types/index';

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

// Build pagination links from meta
const paginationLinks = computed(() => {
    const links: PaginationLink[] = [];
    const meta = props.pages.meta;
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
    <Head title="Pages" />

    <AppLayout :breadcrumbs="breadcrumbs">
    <div>
      <div v-for="page in props.pages.data" :key="page.id">
        {{ page.title }}
      </div>
      <pagination :links="paginationLinks" />
    </div>

    </AppLayout>
</template>
