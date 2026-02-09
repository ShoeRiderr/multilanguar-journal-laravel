<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { computed } from 'vue';

import NavBar from '@/components/user_page/NavBar.vue';
import Footer from '@/components/user_page/Footer.vue';
import { useTrans } from '@/composables/trans';
import { type Page as PageType } from '@/types';

interface Props {
  canRegister: boolean;
  pages?: PageType[];
}

const props = withDefaults(defineProps<Props>(), {
  canRegister: true,
  pages: () => [],
});

const content = computed(() => useTrans('terms_of_service.content'));
const title = computed(() => useTrans('terms_of_service.title'));
</script>

<template>
  <div>
    <Head :title="title" />
    <NavBar :can-register="props.canRegister" :pages="props.pages" />
    <main class="container mx-auto py-8">
      <article v-if="content" v-html="content" class="markdown-body max-w-none" />
      <div v-else class="text-center text-gray-500">{{ useTrans('loading') }}</div>
    </main>
    <Footer />
  </div>
</template>
