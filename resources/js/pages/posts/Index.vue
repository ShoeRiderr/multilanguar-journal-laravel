<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import NavBar from '@/components/user_page/NavBar.vue';
import Footer from '@/components/user_page/Footer.vue';
import Post from '@/components/user_page/Post.vue';
import Pagination from '@/shared/Pagination.vue';
import { type PostsResponse, type PaginationLink, Page as PageType, Language, Category } from '@/types';
import FilterForm from '@/components/user_page/posts/FilterForm.vue';
import { useTrans } from '@/composables/trans';

interface Props {
    posts: PostsResponse;
    pages?: PageType[];
    canRegister: boolean;
    languages: Language[];
    categories: {
        data: Category[]
    };
    filters?: {
        search?: string;
        categories?: number[];
        languages?: number[];
        date_from?: string;
        date_to?: string;
    };
}

const props = withDefaults(
    defineProps<Props>(),
    {
        canRegister: true,
        pages: () => [],
        posts: () => ({ data: [], meta: { current_page: 1, last_page: 1, path: '', from: 0, to: 0, per_page: 0, total: 0 } }),
        languages: () => [],
        categories: () => ({ data: [] }),
        filters: () => ({
            search: '',
            categories: [],
            languages: [],
            date_from: '',
            date_to: '',
        }),
    }
);

const page = usePage();
const locale = computed(() => page.props.locale as string);
</script>

<template>
    <NavBar :can-register="props.canRegister" :pages="props.pages" />
    <div
        :class="['bg-background-light', 'dark:bg-background-dark', 'text-slate-900', 'dark:text-slate-100', 'min-h-screen', 'flex', 'flex-col', 'transition-colors', 'duration-200']">
        <main class="flex-1 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 w-full">
            <div class="flex flex-col lg:flex-row gap-8">
                <FilterForm :languages="props.languages" :categories="props.categories.data" :filters="props.filters" />
                <section class="flex-1">
                    <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                        <div>
                            <h1 class="text-2xl font-bold dark:text-white">{{ useTrans('posts.filtered_by.recently_published') }}</h1>
                            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">{{ useTrans('posts.filter_result_description', { count: props.posts.meta.total }) }}</p>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-2 gap-6">
                        <Post v-for="post in props.posts.data" :key="post.id" :post="post" />
                    </div>
                    <Pagination :meta="props.posts.meta" class="flex items-center justify-center mt-6" />
                </section>
            </div>
        </main>
        <Footer />
    </div>
</template>
