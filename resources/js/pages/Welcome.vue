<script setup lang="ts">
import { logout, login, register } from '@/routes';
import { computed, ref, onMounted, onBeforeUnmount, type Ref } from 'vue';
import { Head, usePage, Link } from '@inertiajs/vue3';
import { type NavItem } from '@/types';
import { LogOut } from 'lucide-vue-next';
import { useTrans } from '@//composables/trans';
import type { Post } from '@/types/index';
const page = usePage();
const locale = computed(() => page.props.locale as string);
const languages = computed(() => page.props.languages as Array<{ id: number; code: string; name: string }>);

interface Props {
    posts: {
        data: Post[];
    };
    canRegister: boolean;
}

withDefaults(
    defineProps<Props>(),
    {
        canRegister: true,
    },
);

function switchLanguage(languageCode: string) {
    const currentUrl = window.location.pathname;
    const pathParts = currentUrl.split('/');
    
    // Replace the locale in the URL
    if (pathParts[1] && pathParts[1].length === 2) {
        pathParts[1] = languageCode;
    } else {
        pathParts.splice(1, 0, languageCode);
    }
    
    window.location.href = pathParts.join('/');
}

const dropdownOpen: Ref<boolean> = ref(false);
const authDropdownOpen: Ref<boolean> = ref(false);
const isDarkMode: Ref<boolean> = ref(false);

function toggleDropdown(): void {
  dropdownOpen.value = !dropdownOpen.value;
}

function toggleAuthDropdown(): void {
  authDropdownOpen.value = !authDropdownOpen.value;
}

function toggleDarkMode(): void {
  isDarkMode.value = !isDarkMode.value;
  if (typeof window !== 'undefined') {
    document.documentElement.classList.toggle('dark', isDarkMode.value);
  }
}

function clickOutsideHandler(e: MouseEvent): void {
  // close the dropdown when clicking outside dropdown-trigger
  if (!(e.target instanceof Element)) return;
  if (!e.target.closest('.dropdown-trigger')) {
    dropdownOpen.value = false;
  }
  if (!e.target.closest('.auth-dropdown-trigger')) {
    authDropdownOpen.value = false;
  }
}

onMounted(() => {
  window.addEventListener('click', clickOutsideHandler);
  if (typeof window !== 'undefined') {
    isDarkMode.value = document.documentElement.classList.contains('dark');
  }
});
onBeforeUnmount(() => {
  window.removeEventListener('click', clickOutsideHandler);
});

const mainNavItems = computed<NavItem[]>(() => [
    {
        title: useTrans('sidebar.posts'),
        href: `/${locale.value}/posts`,
    },
]);
</script>

<template>
    <Head title="Welcome" />

    <nav class="sticky top-0 z-50 bg-white/80 dark:bg-slate-900/80 backdrop-blur-md border-b border-slate-200 dark:border-slate-800">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16 items-center">
          <div class="flex items-center space-x-8">
            <a class="flex items-center space-x-2 text-primary font-bold text-xl hover:cursor-pointer" :href="`/${locale}`">
              <span class="material-icons">auto_stories</span>
              <span class="hidden sm:inline">CodeItAfterMe</span>
            </a>
            <div v-for="item in mainNavItems" :key="item.title" class="hidden md:flex space-x-6">
              <Link :href="item.href" class="text-sm font-medium hover:text-primary transition-colors" :key="item.title">
                  <component :is="item.icon" />
                  <span>{{ item.title }}</span>
              </Link>
            </div>
          </div>

          <div class="flex items-center space-x-4">
            <button class="p-2 text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-full transition-all hover:cursor-pointer" @click="toggleDarkMode">
              <span class="material-icons" v-if="!isDarkMode">dark_mode</span>
              <span class="material-icons" v-else>light_mode</span>
            </button>

            <div class="relative">
              <button class="dropdown-trigger flex items-center space-x-2 px-3 py-1.5 border border-slate-200 dark:border-slate-700 rounded-md hover:bg-slate-50 dark:hover:bg-slate-800 transition-all text-sm font-medium hover:cursor-pointer"
                      @click.stop="toggleDropdown">
                <span class="material-icons text-sm">language</span>
                <span>{{ locale.toUpperCase() }}</span>
                <span class="material-icons text-sm">expand_more</span>
              </button>

              <div :class="['dropdown-content', 'absolute', 'right-0', 'mt-2', 'w-40', 'rounded-lg', 'shadow-xl', 'py-1', 'overflow-hidden', 'z-50', 'bg-white', 'dark:bg-slate-800', 'border', 'border-slate-200', 'dark:border-slate-700', dropdownOpen ? '' : 'hidden']" id="langDropdown">
                <a
                  v-for="language in languages"
                  :key="language.code"
                  href="#"
                  @click.prevent="switchLanguage(language.code)"
                  :class="[
                    'flex items-center justify-between px-4 py-2 text-sm hover:bg-slate-50 dark:hover:bg-slate-700 rounded transition-all',
                    language.code === locale ? 'text-primary font-semibold' : 'text-slate-600 dark:text-slate-300'
                  ]"
                >
                  {{ language.name }} <span>{{ language.code.toUpperCase() }}</span>
                </a>
              </div>
            </div>

            <Link
                v-if="$page.props.auth.user"
                class="flex w-full cursor-pointer"
                :href="logout({locale: locale})"
                as="button"
                data-test="logout-button"
            >
              <LogOut class="mr-2 h-4 w-4" >
              Log out
              </LogOut>
            </Link>
            <template v-else>
              <div class="relative sm:hidden">
                <button
                  class="auth-dropdown-trigger flex items-center space-x-2 px-3 py-1.5 border border-slate-200 dark:border-slate-700 rounded-md hover:bg-slate-50 dark:hover:bg-slate-800 transition-all text-sm font-medium hover:cursor-pointer"
                  @click.stop="toggleAuthDropdown"
                >
                  <span class="material-icons text-sm">account_circle</span>
                  <span>Account</span>
                  <span class="material-icons text-sm">expand_more</span>
                </button>

                <div
                  :class="[
                    'absolute',
                    'right-0',
                    'mt-2',
                    'w-40',
                    'rounded-lg',
                    'shadow-xl',
                    'py-1',
                    'overflow-hidden',
                    'z-50',
                    'bg-white',
                    'dark:bg-slate-800',
                    'border',
                    'border-slate-200',
                    'dark:border-slate-700',
                    authDropdownOpen ? '' : 'hidden'
                  ]"
                >
                  <Link
                    :href="login({locale: locale})"
                    class="flex items-center justify-between px-4 py-2 text-sm hover:bg-slate-50 dark:hover:bg-slate-700 rounded transition-all text-slate-600 dark:text-slate-300"
                  >
                    Log in
                  </Link>
                  <Link
                    v-if="canRegister"
                    :href="register({locale: locale})"
                    class="flex items-center justify-between px-4 py-2 text-sm hover:bg-slate-50 dark:hover:bg-slate-700 rounded transition-all text-slate-600 dark:text-slate-300"
                  >
                    Register
                  </Link>
                </div>
              </div>

              <div class="hidden sm:flex items-center space-x-2">
                <Link
                    :href="login({locale: locale})"
                    class="inline-block rounded-sm border border-transparent px-5 py-1.5 text-sm leading-normal text-[#1b1b18] hover:border-[#19140035] dark:text-[#EDEDEC] dark:hover:border-[#3E3E3A]"
                >
                    Log in
                </Link>
                <Link
                    v-if="canRegister"
                    :href="register({locale: locale})"
                    class="inline-block rounded-sm border border-[#19140035] px-5 py-1.5 text-sm leading-normal text-[#1b1b18] hover:border-[#1915014a] dark:border-[#3E3E3A] dark:text-[#EDEDEC] dark:hover:border-[#62605b]"
                >
                    Register
                </Link>
              </div>
            </template>
          </div>
        </div>
      </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
      <section class="text-center py-16 md:py-24 bg-gradient-to-b from-blue-50/50 to-transparent dark:from-blue-900/10 dark:to-transparent rounded-3xl mb-16">
        <div class="max-w-3xl mx-auto px-4">
          <h1 class="text-4xl md:text-6xl font-extrabold text-slate-900 dark:text-white tracking-tight mb-6">
            Insights for the <span class="text-primary">Global</span> Professional
          </h1>
          <p class="text-lg md:text-xl text-slate-600 dark:text-slate-400 mb-10 leading-relaxed">
            Welcome to CodeItAfterMe. Discover curated content, technical deep-dives, and industry news translated for a worldwide audience. Stay informed, stay ahead.
          </p>

          <div class="flex flex-col sm:flex-row justify-center items-center space-y-4 sm:space-y-0 sm:space-x-4">
            <Link
              :href="`/${locale}/posts`"
              class="w-full sm:w-auto px-8 py-3.5 bg-primary text-white font-semibold rounded-lg hover:bg-blue-600 transition-all shadow-lg shadow-blue-500/25"
            >
              Explore Posts
            </Link>
            <button class="w-full sm:w-auto px-8 py-3.5 border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 font-semibold rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition-all">
              Learn More
            </button>
          </div>
        </div>
      </section>

      <div class="flex items-center justify-between mb-8 border-b border-slate-200 dark:border-slate-800 pb-4">
        <div class="flex items-center space-x-3">
          <span class="material-icons text-primary">feed</span>
          <h2 class="text-2xl font-bold">Latest Posts</h2>
        </div>
        <div class="flex items-center space-x-2 text-sm font-medium text-slate-500">
          <span>Filtered by:</span>
          <span class="bg-blue-100 dark:bg-blue-900/30 text-primary px-3 py-1 rounded-full text-xs">Recently Published</span>
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <!-- Example article 1 -->
        <article v-for="post in posts.data" :key="post.id" class="group bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl overflow-hidden hover:shadow-xl transition-all duration-300">
          <div class="aspect-video bg-slate-100 dark:bg-slate-900 overflow-hidden">
            <img alt="Post thumbnail" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" src="https://lh3.googleusercontent.com/aida-public/AB6AXuBycmUbmTyqHkWB7uY6yFSd4hdqpjgyb3rS8xa11z84ahrjth75h6RkYjPDsxBp_XMyFZlE3l1DzHW5KSei0J8v0zZGexWs_JjbW2__Ee7q-19UmSiVEZCA9nUEQjCBwgxF0y3_e6pjpk0k2lnvDu44FWAKNI6JgdfMGuTAO1sVNE9q1_hiTVeYCbMnD3t48Y7fi6dExHYUlYzXMQ1tiaUxm5nfbFqg99LbgVCOhvaRh3pe-SdkeLIe_LktykNSeBCNvnJ9b-ceC3LB" />
          </div>
          <div class="p-6">
            <div class="flex items-center justify-between mb-3">
              <span class="text-xs font-bold uppercase tracking-wider text-primary">{{post.categories[0].name}}</span>
              <span class="text-xs text-slate-400 flex items-center"><span class="material-icons text-sm mr-1">visibility</span> 1.2k</span>
            </div>
            <h3 class="text-xl font-bold mb-3 group-hover:text-primary transition-colors">{{post.title}}</h3>
            <p class="text-slate-600 dark:text-slate-400 text-sm line-clamp-3 mb-6">{{post.content_md}}</p>
            <div class="flex items-center justify-between pt-6 border-t border-slate-100 dark:border-slate-700">
              <div class="flex items-center space-x-2">
                <div class="w-8 h-8 rounded-full bg-blue-500 flex items-center justify-center text-white text-xs font-bold">JD</div>
                <span class="text-xs font-medium text-slate-500">John Doe</span>
              </div>
              <span class="text-xs text-slate-400 italic">2 days ago</span>
            </div>
          </div>
        </article>

        <!-- Example article 2 -->
        <article class="group bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl overflow-hidden hover:shadow-xl transition-all duration-300">
          <div class="aspect-video bg-slate-100 dark:bg-slate-900 overflow-hidden">
            <img alt="Data analytics" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" src="https://lh3.googleusercontent.com/aida-public/AB6AXuCpB2iKrfu5AWd2oN9LLrmsOfhcbILPA-abNv4JE4FRai2xjjoRlBevlC5WDW-mCQ9DL8PQ85Ip_XO13tsrqtatVG8VoWRuEk-Y8DYaDSTBNz03ifw-rT8FIHK-ZMD5ry4D4NFX9yP2rBKZ43HsfzEg9wy-nK-Jq7L7wjrPItehDM632GYjOqG3z9TeLk_p3nqx-2GTea7H6RbdEfCuI-hUR3KnENcKboMp7mdIcqPWzNFpLmY3_87FesjAzpeCYhiYQAyh0Jc3FlsJ" />
          </div>
          <div class="p-6">
            <div class="flex items-center justify-between mb-3">
              <span class="text-xs font-bold uppercase tracking-wider text-primary">Business</span>
              <span class="text-xs text-slate-400 flex items-center"><span class="material-icons text-sm mr-1">visibility</span> 850</span>
            </div>
            <h3 class="text-xl font-bold mb-3 group-hover:text-primary transition-colors">International Market Strategies</h3>
            <p class="text-slate-600 dark:text-slate-400 text-sm line-clamp-3 mb-6">Navigating the complexities of global trade and localization...</p>
            <div class="flex items-center justify-between pt-6 border-t border-slate-100 dark:border-slate-700">
              <div class="flex items-center space-x-2">
                <div class="w-8 h-8 rounded-full bg-green-500 flex items-center justify-center text-white text-xs font-bold">AS</div>
                <span class="text-xs font-medium text-slate-500">Anna Smith</span>
              </div>
              <span class="text-xs text-slate-400 italic">May 12, 2024</span>
            </div>
          </div>
        </article>

        <!-- Example article 3 -->
        <article class="group bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl overflow-hidden hover:shadow-xl transition-all duration-300">
          <div class="aspect-video bg-slate-100 dark:bg-slate-900 overflow-hidden">
            <img alt="Productivity" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" src="https://lh3.googleusercontent.com/aida-public/AB6AXuCedoNZDC5PBGis3TB_AQmsESqqY1_eecYfWXrLDLHIoupi2Ai2vRwyvq5jgUf3RGQiF-jGwCHIupwhIpWRllesh8sxBvgTRr6ZVgp0JicuTIuIAuFJvTzRQ2VFWdWtjpegya-0E0Cot8AZewqQCsLksZ-TWfuDItRg04TFcO1tkJ-ilO82INLYiG2WpgMOUcBvO2MXMYCbGtVjFfNCVWAqoDl-PXY3tbE8ioI9ETpVcXfXnkI0IAP4sS9je0AvX3ODFmipUZbA2sk5" />
          </div>
          <div class="p-6">
            <div class="flex items-center justify-between mb-3">
              <span class="text-xs font-bold uppercase tracking-wider text-primary">Productivity</span>
              <span class="text-xs text-slate-400 flex items-center"><span class="material-icons text-sm mr-1">visibility</span> 2.4k</span>
            </div>
            <h3 class="text-xl font-bold mb-3 group-hover:text-primary transition-colors">Remote Collaboration Best Practices</h3>
            <p class="text-slate-600 dark:text-slate-400 text-sm line-clamp-3 mb-6">Discover the tools and mindsets necessary to keep a distributed team synchronized...</p>
            <div class="flex items-center justify-between pt-6 border-t border-slate-100 dark:border-slate-700">
              <div class="flex items-center space-x-2">
                <div class="w-8 h-8 rounded-full bg-purple-500 flex items-center justify-center text-white text-xs font-bold">MK</div>
                <span class="text-xs font-medium text-slate-500">Mark King</span>
              </div>
              <span class="text-xs text-slate-400 italic">April 28, 2024</span>
            </div>
          </div>
        </article>
      </div>

      <div class="mt-16 text-center">
        <button class="inline-flex items-center space-x-2 px-6 py-3 border border-slate-200 dark:border-slate-700 rounded-lg font-medium hover:bg-slate-100 dark:hover:bg-slate-800 transition-all">
          <span>View All Articles</span>
          <span class="material-icons text-sm">arrow_forward</span>
        </button>
      </div>
    </main>

    <footer class="bg-white dark:bg-slate-900 border-t border-slate-200 dark:border-slate-800 py-12 mt-20">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
          <div class="col-span-1 md:col-span-1">
            <a class="flex items-center space-x-2 text-primary font-bold text-xl mb-4" href="#">
              <span class="material-icons">auto_stories</span>
              <span>LuminaCMS</span>
            </a>
            <p class="text-sm text-slate-500 dark:text-slate-400 leading-relaxed">
              A dynamic content management platform supporting multiple languages and seamless reading experiences.
            </p>
          </div>

          <div>
            <h4 class="font-bold text-sm uppercase tracking-wider mb-4">Navigation</h4>
            <ul class="space-y-2 text-sm text-slate-500 dark:text-slate-400">
              <li><a class="hover:text-primary" href="#">Home</a></li>
              <li><a class="hover:text-primary" href="#">Archive</a></li>
              <li><a class="hover:text-primary" href="#">Categories</a></li>
              <li><a class="hover:text-primary" href="#">RSS Feed</a></li>
            </ul>
          </div>

          <div>
            <h4 class="font-bold text-sm uppercase tracking-wider mb-4">Support</h4>
            <ul class="space-y-2 text-sm text-slate-500 dark:text-slate-400">
              <li><a class="hover:text-primary" href="#">Help Center</a></li>
              <li><a class="hover:text-primary" href="#">Privacy Policy</a></li>
              <li><a class="hover:text-primary" href="#">Terms of Service</a></li>
              <li><a class="hover:text-primary" href="#">Cookie Settings</a></li>
            </ul>
          </div>

          <div>
            <h4 class="font-bold text-sm uppercase tracking-wider mb-4">Stay Updated</h4>
            <div class="flex">
              <input class="w-full px-4 py-2 bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-l-md text-sm focus:ring-primary focus:border-primary" placeholder="Email address" type="email"/>
              <button class="bg-primary px-4 py-2 text-white rounded-r-md">
                <span class="material-icons text-sm">send</span>
              </button>
            </div>
          </div>
        </div>

        <div class="mt-12 pt-8 border-t border-slate-100 dark:border-slate-800 flex flex-col md:flex-row justify-between items-center text-xs text-slate-400">
          <p>© 2024 LuminaCMS Content Platform. All rights reserved.</p>
          <div class="flex space-x-6 mt-4 md:mt-0">
            <a class="hover:text-primary" href="#">Twitter</a>
            <a class="hover:text-primary" href="#">LinkedIn</a>
            <a class="hover:text-primary" href="#">GitHub</a>
          </div>
        </div>
      </div>
    </footer>
</template>

<style scoped>
/* Scoped adjustments if needed (kept minimal to prefer tailwind classes) */
</style>