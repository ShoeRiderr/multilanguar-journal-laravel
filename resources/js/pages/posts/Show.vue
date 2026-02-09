<script setup lang="ts">
import NavBar from '@/components/user_page/NavBar.vue';
import Footer from '@/components/user_page/Footer.vue';
import { type Post, type Page as PageType, type Language } from '@/types';
import { useTrans } from '@/composables/trans';
import { usePage, Link } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { formatDateShort, estimateReadTime } from '@/lib/utils';
import UserLogo from '@/components/user_page/UserLogo.vue';

interface Props {
    post: {
        data: Post
    };
    canRegister: boolean;
    pages?: PageType[];
}

const props = withDefaults(
    defineProps<Props>(),
    {
        canRegister: true,
        pages: () => [],
    }
);

const page = usePage();
const locale = computed(() => page.props.locale as string);
const postsLink = computed(() => {
    return `/${locale.value}/posts`;
});
const languageName = computed(() => {
    const languages = page.props.languages as Language[] | undefined;
    const match = languages?.find((language) => language.id === props.post.data.language_id);
    return match?.name ?? String(props.post.data.language_id ?? '');
});

const currentUrl = computed(() => (typeof window !== 'undefined' ? window.location.href : ''));
const shareTitle = computed(() => props.post.data.title ?? '');
const shareText = computed(() => `${shareTitle.value}`.trim());
const copyState = ref<'idle' | 'copied' | 'error'>('idle');
let copyTimer: number | null = null;
const shareNative = async () => {
    if (typeof navigator === 'undefined') return;
    if (navigator.share) {
        await navigator.share({
            title: shareTitle.value,
            text: shareText.value,
            url: currentUrl.value,
        });
        return;
    }
    await copyLink();
};

const shareWhatsapp = () => {
    if (!currentUrl.value) return;
    const text = encodeURIComponent(`${shareText.value} ${currentUrl.value}`.trim());
    window.open(`https://wa.me/?text=${text}`, '_blank', 'noopener');
};

const shareGmail = () => {
    if (!currentUrl.value) return;
    const subject = encodeURIComponent(shareTitle.value);
    const body = encodeURIComponent(`${shareText.value}\n${currentUrl.value}`.trim());
    window.open(`https://mail.google.com/mail/?view=cm&fs=1&su=${subject}&body=${body}`, '_blank', 'noopener');
};

const copyLink = async () => {
    if (!currentUrl.value) return;
    copyState.value = 'idle';
    try {
        if (navigator?.clipboard?.writeText) {
            await navigator.clipboard.writeText(currentUrl.value);
        } else {
            const el = document.createElement('textarea');
            el.value = currentUrl.value;
            el.setAttribute('readonly', '');
            el.style.position = 'absolute';
            el.style.left = '-9999px';
            document.body.appendChild(el);
            el.select();
            document.execCommand('copy');
            document.body.removeChild(el);
        }
        copyState.value = 'copied';
    } catch {
        copyState.value = 'error';
    }
    if (copyTimer) window.clearTimeout(copyTimer);
    copyTimer = window.setTimeout(() => {
        copyState.value = 'idle';
        copyTimer = null;
    }, 2000);
};

</script>

<template>
    <NavBar :can-register="props.canRegister" :pages="props.pages" />

    <main class="flex-1 w-full max-w-[1200px] mx-auto px-6 py-10">
        <div class="flex flex-col lg:flex-row gap-12">
            <article class="flex-1 max-w-[800px] mx-auto">
                <div class="w-full aspect-video rounded-xl overflow-hidden mb-8 shadow-lg dark:shadow-2xl">
                    <img alt="Coding workspace with laptop" class="w-full h-full object-cover"
                        :src="post.data.main_photo?.url ?? 'https://lh3.googleusercontent.com/aida-public/AB6AXuCPREG6PY4o-Vu_mJ_VCzLGNeOjmN1UPiupuIJ34z_tM6coyFEVWrBmeJrkfcCsoCNM2wS9pBgwLBF9jivxlMvKLHZmRhKFHsS5tY1Vn2NfeIztEIFk1TnexUtgwsoMNrfdIgdxSrjg1HnIuVChsv3cCUwo6pxvF7PbAf7uF1CRpatiZMWVv_1djSEREO41-g8NxpEeYV5LIUuk8GuOh-NG2gcA9lkhZURB71ek7QxV-E0NMoaXEcDHJrIN8_FQzZO0NMfRvB0RfUPz'" />
                </div>
                <h1 class="text-4xl md:text-5xl font-extrabold leading-tight mb-6 text-black dark:text-white">
                    {{ post.data.title }}
                </h1>
                <div
                    class="flex items-center justify-between py-6 border-y border-slate-200 dark:border-slate-800 mb-8">
                    <div class="flex items-center gap-4">
                        <UserLogo class="w-12 h-12 rounded-full overflow-hidden border-2 border-primary/20" />
                        <div>
                            <p class="font-bold text-slate-900 dark:text-white">Kamil</p>
                            <p class="text-sm text-slate-500 dark:text-slate-400">Creator of CodeItAfterMe</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-medium text-slate-700 dark:text-slate-300">
                            {{ useTrans('posts.published') }} {{ formatDateShort(post.data.published_at) }}
                        </p>
                        <p class="text-xs text-slate-500 dark:text-slate-400">
                            {{ estimateReadTime(post.data.content_md) }}
                        </p>
                    </div>
                </div>
                <div class="prose prose-slate dark:prose-invert max-w-none text-lg leading-relaxed space-y-6">
                    <div v-html="post.data.content_md" class="text-slate-600 dark:text-slate-400 text-md mb-6"></div>
                </div>
                <div
                    class="mt-12 pt-8 border-t border-slate-200 dark:border-slate-800 flex flex-wrap gap-4 items-center">
                    <span class="text-sm font-bold uppercase tracking-wider text-slate-500">{{
                        useTrans('categories') }}:</span>
                    <div class="flex flex-wrap gap-2">
                        <span v-for="category in post.data.categories?.data" :key="category.id"
                            class="px-3 py-1 bg-primary/10 text-primary text-xs font-bold rounded-full border border-primary/20">{{
                                category.name }}</span>
                    </div>
                    <div class="ml-auto flex items-center gap-0 text-slate-500 dark:text-slate-400 text-sm">
                        <span class="material-icons text-sm mr-2">language</span>
                        <span>{{ useTrans('posts.language') }}: {{ languageName }}</span>
                    </div>
                </div>
                <div class="mt-10 mb-20 flex justify-center">
                    <Link :href="postsLink"
                        class="group flex items-center px-8 py-3 bg-primary text-white font-bold rounded-lg hover:bg-primary/90 transition-all shadow-lg shadow-primary/20 space-x-2">
                        <span
                            class="material-icons text-sm group-hover:-translate-x-1 transition-transform">arrow_back</span>
                        <span>{{ useTrans('posts.back_to_posts') }}</span>
                    </Link>
                </div>
            </article>
            <aside class="hidden lg:block w-[300px]">
                <div class="sticky top-24 space-y-8">
                    <div
                        class="p-6 bg-slate-50 dark:bg-slate-900 rounded-xl border border-slate-100 dark:border-slate-800">
                        <h3 class="font-bold text-lg mb-4 text-slate-900 dark:text-white">
                            {{ useTrans('posts.share_post') }}
                        </h3>
                        <div class="grid grid-cols-4 gap-3">
                            <button type="button" @click="shareNative"
                                class="size-10 flex items-center justify-center rounded-lg bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:text-primary dark:hover:text-primary transition-colors hover:cursor-pointer"
                                aria-label="Share">
                                <span class="material-icons text-[20px]">share</span>
                            </button>
                            <button type="button" @click="shareWhatsapp"
                                class="size-10 flex items-center justify-center rounded-lg bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:text-primary dark:hover:text-primary transition-colors hover:cursor-pointer"
                                aria-label="Share on WhatsApp">
                                <span class="material-icons text-[20px]">chat</span>
                            </button>
                            <button type="button" @click="shareGmail"
                                class="size-10 flex items-center justify-center rounded-lg bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:text-primary dark:hover:text-primary transition-colors hover:cursor-pointer"
                                aria-label="Share via Gmail">
                                <span class="material-icons text-[20px]">mail</span>
                            </button>
                            <button type="button" @click="copyLink"
                                class="size-10 flex items-center justify-center rounded-lg bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:text-primary dark:hover:text-primary transition-colors hover:cursor-pointer"
                                aria-label="Copy link">
                                <span class="material-icons text-[20px]">content_copy</span>
                            </button>
                        </div>
                        <p v-if="copyState === 'copied'" class="mt-3 text-xs font-medium text-emerald-600">
                            {{ useTrans('posts.link_copied') ?? 'Link copied' }}
                        </p>
                        <p v-else-if="copyState === 'error'" class="mt-3 text-xs font-medium text-rose-600">
                            {{ useTrans('posts.link_copy_failed') ?? 'Could not copy link' }}
                        </p>
                    </div>
                    <div
                        class="p-6 bg-slate-50 dark:bg-slate-900 rounded-xl border border-slate-100 dark:border-slate-800">
                        <h3 class="font-bold text-lg mb-4 text-slate-900 dark:text-white">{{ useTrans('posts.post_info')
                        }}</h3>
                        <ul class="space-y-4 text-sm">
                            <li class="flex justify-between">
                                <span class="text-slate-500">{{ useTrans('posts.language') }}</span>
                                <span class="font-medium text-slate-800 dark:text-slate-200">{{ languageName }}</span>
                            </li>
                            <li class="flex justify-between">
                                <span class="text-slate-500">{{ useTrans('posts.last_updated') }}</span>
                                <span class="font-medium text-slate-800 dark:text-slate-200">{{
                                    formatDateShort(post.data.updated_at) }}</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </aside>
        </div>
    </main>
    <Footer />
</template>