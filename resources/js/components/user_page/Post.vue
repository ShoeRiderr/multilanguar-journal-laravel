<script setup lang="ts">
import { dateDifference } from '@/lib/utils';
import type { Post } from '@/types/index';

interface Props {
    post: Post;
}

defineProps<Props>();
</script>

<template>
    <article class="group bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl overflow-hidden hover:shadow-xl transition-all duration-300">
      <div class="aspect-video bg-slate-100 dark:bg-slate-900 overflow-hidden hover:cursor-pointer">
        <img
          v-if="post.main_photo && post.main_photo.url"
          :src="post.main_photo.url"
          :alt="post.main_photo.file_name || 'Post thumbnail'"
          class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
        />
        <div v-else class="w-full h-full flex items-center justify-center bg-slate-200 dark:bg-slate-700 text-slate-400">
          <span>No image</span>
        </div>
      </div>
      <div class="p-6">
        <div class="flex items-center justify-between mb-3">
          <span class="text-xs font-bold uppercase tracking-wider text-primary hover:cursor-pointer">{{ post.categories[0].name }}</span>
          <span class="text-xs text-slate-400 flex items-center hover:cursor-pointer"><span class="material-icons text-sm mr-1">visibility</span> {{ post.post_view?.view_count }}</span>
        </div>
        <h3 class="text-xl font-bold mb-3 group-hover:text-primary transition-colors hover:cursor-pointer">{{ post.title }}</h3>
        <div v-html="post.content_md" class="text-slate-600 dark:text-slate-400 text-sm line-clamp-3 mb-6"></div>
        <div class="flex items-center justify-between pt-6 border-t border-slate-100 dark:border-slate-700">
          <div class="flex items-center space-x-2">
            <div class="w-8 h-8 rounded-full bg-blue-500 flex items-center justify-center text-white text-xs font-bold">KL</div>
            <span class="text-xs font-medium text-slate-500">Kamil</span>
          </div>
          <span class="text-xs text-slate-400 italic">{{ dateDifference(post.published_at!) }}</span>
        </div>
      </div>
    </article>
</template> 