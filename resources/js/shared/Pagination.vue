<template>
  <div v-if="links.length > 1" :class="['pagination-container', $attrs.class]">
    <div class="flex flex-wrap -mb-1">
      <template v-for="(link, key) in links">
        <div
          v-if="link.url === null"
          :key="key"
          :class="['mb-1 mr-1 px-4 py-3 text-sm leading-4 border rounded',
            'pagination-link',
            'dark:text-slate-400 dark:border-slate-700',
            'text-gray-400 border-slate-200']"
          v-html="link.label"
        />
        <Link
          v-else
          :key="`link-${key}`"
          :class="[
            'mb-1 mr-1 px-4 py-3 text-sm leading-4 border rounded focus:text-indigo-500 hover:bg-white focus:border-indigo-500',
            'pagination-link',
            link.active
              ? 'bg-primary text-white dark:bg-slate-900 dark:text-white border-primary dark:border-primary font-bold'
              : 'text-slate-700 dark:text-slate-300 border-slate-200 dark:border-slate-700',
          ]"
          :href="link.url"
          v-html="link.label"
        />
      </template>
    </div>
  </div>
</template>

<script lang="ts">

import { Link } from '@inertiajs/vue3';
import { PropType } from 'vue';
import { useTrans } from '@/composables/trans';

export default {
  components: {
    Link,
  },
  props: {
    meta: {
      type: Object as PropType<{
        current_page: number;
        last_page: number;
        path: string;
      }>,
      required: true,
    },
  },
  computed: {
    links(): any[] {
      const meta = this.meta;
      const path = meta.path;
      const current = meta.current_page;
      const last = meta.last_page;
      const links: any[] = [];

      // Previous page
      if (current > 1) {
        links.push({
          url: `${path}?page=${current - 1}`,
          label: `← ${useTrans('pagination.previous')}`,
          active: false,
        });
      }

      // Always show first page
      if (current > 3) {
        links.push({
          url: path,
          label: '1',
          active: current === 1,
        });
        if (current > 4) {
          links.push({ url: null, label: '...', active: false });
        }
      }

      // Show two pages before current
      for (let i = Math.max(1, current - 2); i < current; i++) {
        if (i === 1 && current <= 3) continue; // already shown
        links.push({
          url: i === 1 ? path : `${path}?page=${i}`,
          label: i.toString(),
          active: false,
        });
      }

      // Current page
      links.push({
        url: `${path}?page=${current}`,
        label: current.toString(),
        active: true,
      });

      // Show two pages after current
      for (let i = current + 1; i <= Math.min(last, current + 2); i++) {
        links.push({
          url: `${path}?page=${i}`,
          label: i.toString(),
          active: false,
        });
      }

      // Ellipsis and last page
      if (current < last - 2) {
        if (current < last - 3) {
          links.push({ url: null, label: '...', active: false });
        }
        links.push({
          url: `${path}?page=${last}`,
          label: last.toString(),
          active: current === last,
        });
      }

      // Next page
      if (current < last) {
        links.push({
          url: `${path}?page=${current + 1}`,
          label: `${useTrans('pagination.next')} →`,
          active: false,
        });
      }

      return links;
    },
  },
};
</script>


<style scoped>
.pagination-container {
  /* Add transition for color changes */
  transition: background-color 0.2s, color 0.2s;
}
.pagination-link {
  transition: background-color 0.2s, color 0.2s, border-color 0.2s;
}
</style>