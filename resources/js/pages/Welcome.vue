<script setup lang="ts">
import { computed } from 'vue';
import { Head, usePage, Link } from '@inertiajs/vue3';
import { useTrans } from '@//composables/trans';
import type { Post as PostType, Page as PageType } from '@/types/index';
import Footer from '@/components/user_page/Footer.vue';
import NavBar from '@/components/user_page/NavBar.vue';
import Post from '@/components/user_page/Post.vue';

const page = usePage();
const locale = computed(() => page.props.locale as string);

interface Props {
  posts: {
    data: PostType[];
  };
  canRegister: boolean;
  pages?: PageType[];
}

const props = withDefaults(
  defineProps<Props>(),
  {
    canRegister: true,
    pages: () => [],
  },
);
</script>

<template>
    <Head title="Welcome" />

    <NavBar :can-register="canRegister" :pages="props.pages" />
  
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
      <section class="text-center py-16 md:py-24 bg-gradient-to-b from-blue-50/50 to-transparent dark:from-blue-900/10 dark:to-transparent rounded-3xl mb-16">
        <div class="max-w-3xl mx-auto px-4">
          <h1 class="text-4xl md:text-6xl font-extrabold text-slate-900 dark:text-white tracking-tight mb-6">
            {{ useTrans('welcome.title.part1') }} <span class="text-primary">{{ useTrans('welcome.title.part2') }}</span> {{ useTrans('welcome.title.part3') }}
          </h1>
          <p class="text-lg md:text-xl text-slate-600 dark:text-slate-400 mb-10 leading-relaxed">
            {{ useTrans('welcome.subtitle') }}
          </p>

          <div class="flex flex-col sm:flex-row justify-center items-center space-y-4 sm:space-y-0 sm:space-x-4">
            <Link
              :href="`/${locale}/posts`"
              class="w-full sm:w-auto px-8 py-3.5 bg-primary text-white font-semibold rounded-lg hover:bg-blue-600 transition-all shadow-lg shadow-blue-500/25"
            >
              {{ useTrans('welcome.get_started') }}
            </Link>
            <button class="w-full sm:w-auto px-8 py-3.5 border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 font-semibold rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition-all shadow-md shadow-slate-500/10 hover:cursor-pointer">
              {{ useTrans('welcome.more') }}
            </button>
          </div>
        </div>
      </section>

      <div class="flex items-center justify-between mb-8 border-b border-slate-200 dark:border-slate-800 pb-4">
        <div class="flex items-center space-x-3">
          <span class="material-icons text-primary">feed</span>
          <h2 class="text-2xl font-bold">{{ useTrans('welcome.latest_posts') }}</h2>
        </div>
        <div class="flex items-center space-x-2 text-sm font-medium text-slate-500">
          <span>{{ useTrans('welcome.filtered_by') }}</span>
          <span class="bg-blue-100 dark:bg-blue-900/30 text-primary px-3 py-1 rounded-full text-xs">{{ useTrans('posts.filtered_by.recently_published') }}</span>
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <Post v-for="post in posts.data" :key="post.id" :post="post" />
      </div>

      <div class="mt-16 text-center">
        <button class="inline-flex items-center space-x-2 px-6 py-3 border border-slate-200 dark:border-slate-700 rounded-lg font-medium hover:bg-slate-100 dark:hover:bg-slate-800 transition-all hover:cursor-pointer">
          <span>{{ useTrans('posts.all_articles') }}</span>
          <span class="material-icons text-sm">arrow_forward</span>
        </button>
      </div>
    </main>

    <Footer />
</template>

<style scoped>
/* Scoped adjustments if needed (kept minimal to prefer tailwind classes) */
</style>