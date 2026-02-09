<script lang="ts" setup>
import { useTrans } from '@/composables/trans';
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();
const locale = computed(() => page.props.locale as string);
</script>

<template>
    <footer class="bg-white dark:bg-slate-900 border-t border-slate-200 dark:border-slate-800 py-12 mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <div class="font-bold text-xl text-primary mb-2">
              <a class="hover:cursor-pointer flex items-center justify-center gap-2" :href="`/${locale}`">
                <span class="material-icons">auto_stories</span>
                <span class="sm:inline">CodeItAfterMe</span>
              </a>
            </div>
            <p class="text-sm text-slate-500 dark:text-slate-400">© {{ new Date().getFullYear() }} {{ useTrans('footer.text') }}</p>
            <div class="flex justify-center gap-6 mt-6">
                <a class="text-slate-400 hover:text-primary transition-colors" href="https://github.com/ShoeRiderr"><span class="material-icons-outlined">GitHub</span></a>
                <a class="text-slate-400 hover:text-primary transition-colors" href="https://www.linkedin.com/in/kamill-socha/"><span class="material-icons-outlined">LinkedIn</span></a>
                <a class="text-slate-400 hover:text-primary transition-colors" href="https://www.youtube.com/@Devwov"><span class="material-icons-outlined">YouTube</span></a>
            </div>
            <div class="mt-6">
                <a class="text-sm text-slate-400 hover:text-primary transition-colors" href="/privacy-policy">{{ useTrans('footer.privacy_policy') }}</a>
                <span class="mx-2 text-slate-400">|</span>
                <a class="text-sm text-slate-400 hover:text-primary transition-colors" href="/terms-of-service">{{ useTrans('footer.terms_of_service') }}</a>
            </div>
        </div>
    </footer>
</template>