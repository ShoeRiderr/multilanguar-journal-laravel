
<script lang="ts" setup>
import { computed } from 'vue';
import NavBar from '@/components/user_page/NavBar.vue';
import Footer from '@/components/user_page/Footer.vue';
import type { Page as PageType, PageResourceType } from '@/types/index';

interface Props {
  page: {
    "data": PageResourceType
  },
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
const content = computed(() => props.page.data.content_md || '');
</script>

<template>
  <div>
    <NavBar :can-register="props.canRegister" :pages="props.pages" />
    <main class="container mx-auto py-8">
      <article v-if="content" v-html="content" class="markdown-body max-w-none" />
      <div v-else class="text-center text-gray-500">Loading...</div>
    </main>
    <Footer />
  </div>
</template>


