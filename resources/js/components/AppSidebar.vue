<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { LayoutGrid, Tags, Book,  } from 'lucide-vue-next';
import { computed } from 'vue';

import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { dashboard } from '@/routes';
import { type NavItem } from '@/types';

import AppLogo from './AppLogo.vue';

const page = usePage();
const user = page.props.auth.user;
const isAdmin = computed(() => (user && user.role === 'admin') ?? false as boolean);
const locale = computed(() => (page.props.locale as string) ?? 'en');
const dashboardUrl = dashboard({locale: locale.value});

const mainNavItems = computed<NavItem[]>(() => [
    {
        title: 'Dashboard',
        href: dashboardUrl,
        icon: LayoutGrid,
    },
    {
        title: 'Categories',
        href: `/${locale.value}/admin/categories`,
        icon: Tags,
        isDisplayed: isAdmin.value,
    },
    {
        title: 'Languages',
        href: `/${locale.value}/admin/languages`,
        icon: Tags,
        isDisplayed: isAdmin.value,
    },
    {
        title: 'Posts',
        href: `/${locale.value}/admin/posts`,
        icon: Book,
        isDisplayed: isAdmin.value,
    },
    {
        title: 'Pages',
        href: `/${locale.value}/admin/pages`,
        icon: Book,
        isDisplayed: isAdmin.value,
    },
]);

const footerNavItems: NavItem[] = [
    // {
    //     title: 'Github Repo',
    //     href: 'https://github.com/laravel/vue-starter-kit',
    //     icon: Folder,
    // },
    // {
    //     title: 'Documentation',
    //     href: 'https://laravel.com/docs/starter-kits#vue',
    //     icon: BookOpen,
    // },
];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="dashboardUrl">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
