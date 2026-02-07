<script setup lang="ts">
import { Head, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import NavBar from '@/components/user_page/NavBar.vue';
import Footer from '@/components/user_page/Footer.vue';
import { type PostsResponse, type PaginationMeta, type PaginationLink, Page as PageType } from '@/types';

interface Props {
    posts: PostsResponse;
    pages?: PageType[];
    canRegister: boolean;
}

const props = withDefaults(
  defineProps<Props>(),
  {
    canRegister: true,
    pages: () => [],
    posts: () => ({ data: [], meta: { current_page: 1, last_page: 1, path: '', from: 0, to: 0, per_page: 0, total: 0 } }),
  }
);

const page = usePage();
const locale = computed(() => page.props.locale as string);
const dashboardUrl = computed(() => `/${locale.value}/dashboard`);

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
    <NavBar :can-register="props.canRegister" :pages="props.pages" />
    <main class="container mx-auto py-8">
      <div v-if="props.posts" class="space-y-4">
          <div v-for="post in props.posts.data" :key="post.id">
            {{ post.title }}
          </div>
          <pagination :links="paginationLinks" />
      </div>
      <div v-else class="text-center text-gray-500">Loading...</div>
    </main>
    <Footer />
</template>
