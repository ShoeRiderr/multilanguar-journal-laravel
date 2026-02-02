<script setup lang="ts">
import { Head, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

import Pagination from '@/shared/Pagination.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { type Post, PaginationLink, PaginationMeta } from '@/types/index';

interface Props {
    posts: {
        data: Post[];
        meta: PaginationMeta;
    };
}

const props = defineProps<Props>();
const page = usePage();
const locale = computed(() => page.props.locale as string);
const dashboardUrl = computed(() => `/${locale.value}/dashboard`);

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Posts',
        href: dashboardUrl.value,
    },
];

// Build pagination links from meta
const paginationLinks = computed(() => {
    const links: PaginationLink[] = [];
    const meta = props.posts.meta;
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
    <Head title="Posts" />

    <AppLayout :breadcrumbs="breadcrumbs">
    <div>
      <div v-for="post in props.posts.data" :key="post.id">
        {{ post.title }}
      </div>
      <pagination :links="paginationLinks" />
    </div>

    </AppLayout>
</template>
