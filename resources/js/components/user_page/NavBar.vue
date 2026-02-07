<script lang="ts" setup>
import { logout, login, register } from '@/routes';
import { computed, ref, onMounted, onBeforeUnmount, type Ref } from 'vue';
import { Head, usePage, Link } from '@inertiajs/vue3';
import { type NavItem, Page as PageType } from '@/types';
import { LogOut } from 'lucide-vue-next';
import { useTrans } from '@//composables/trans';

interface Props {
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
const page = usePage();
const locale = computed(() => page.props.locale as string);
const languages = computed(() => page.props.languages as Array<{ id: number; code: string; name: string }>);


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
const mainNavItems = computed<NavItem[]>(() => [
  {
    title: useTrans('navbar.posts'),
    href: `/${locale.value}/posts`,
  },
  ...props.pages.map(page => ({
    title: page.title,
    href: `/${locale.value}/${page.slug}`,
  })),
]);
</script>

<template>
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
                {{ useTrans('navbar.logout') }}
              </LogOut>
            </Link>
            <template v-else>
              <div class="relative sm:hidden">
                <button
                  class="auth-dropdown-trigger flex items-center space-x-2 px-3 py-1.5 border border-slate-200 dark:border-slate-700 rounded-md hover:bg-slate-50 dark:hover:bg-slate-800 transition-all text-sm font-medium hover:cursor-pointer"
                  @click.stop="toggleAuthDropdown"
                >
                  <span class="material-icons text-sm">account_circle</span>
                  <span>{{ useTrans('navbar.profile') }}</span>
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
                    {{ useTrans('navbar.login') }}
                  </Link>
                  <Link
                    v-if="canRegister"
                    :href="register({locale: locale})"
                    class="flex items-center justify-between px-4 py-2 text-sm hover:bg-slate-50 dark:hover:bg-slate-700 rounded transition-all text-slate-600 dark:text-slate-300"
                  >
                    {{ useTrans('navbar.register') }}
                  </Link>
                </div>
              </div>

              <div class="hidden sm:flex items-center space-x-2">
                <Link
                    :href="login({locale: locale})"
                    class="inline-block rounded-sm border border-transparent px-5 py-1.5 text-sm leading-normal text-[#1b1b18] hover:border-[#19140035] dark:text-[#EDEDEC] dark:hover:border-[#3E3E3A]"
                >
                    {{ useTrans('navbar.login') }}
                </Link>
                <Link
                    v-if="canRegister"
                    :href="register({locale: locale})"
                    class="inline-block rounded-sm border border-[#19140035] px-5 py-1.5 text-sm leading-normal text-[#1b1b18] hover:border-[#1915014a] dark:border-[#3E3E3A] dark:text-[#EDEDEC] dark:hover:border-[#62605b]"
                >
                    {{ useTrans('navbar.register') }}
                </Link>
              </div>
            </template>
          </div>
        </div>
      </div>
    </nav>
</template>