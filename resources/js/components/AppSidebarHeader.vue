<script setup lang="ts">
import { computed } from 'vue';
import { Globe, Monitor, Moon, Sun } from 'lucide-vue-next';
import Breadcrumbs from '@/components/Breadcrumbs.vue';
import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuRadioGroup,
    DropdownMenuRadioItem,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { SidebarTrigger } from '@/components/ui/sidebar';
import { useAppearance } from '@/composables/useAppearance';
import { useTrans } from '@/composables/trans';
import { usePage } from '@inertiajs/vue3';
import type { BreadcrumbItemType } from '@/types';

withDefaults(
    defineProps<{
        breadcrumbs?: BreadcrumbItemType[];
    }>(),
    {
        breadcrumbs: () => [],
    },
);

const { appearance, resolvedAppearance, updateAppearance } = useAppearance();
const page = usePage();
const locale = computed(() => page.props.locale as string);
const languages = computed(
    () => page.props.languages as Array<{ id: number; code: string; name: string }>
);

const themeIcon = computed(() => {
    if (appearance.value === 'system') {
        return Monitor;
    }

    return resolvedAppearance.value === 'dark' ? Moon : Sun;
});

function switchLanguage(languageCode: string) {
    const currentUrl = window.location.pathname;
    const pathParts = currentUrl.split('/');

    if (pathParts[1] && pathParts[1].length === 2) {
        pathParts[1] = languageCode;
    } else {
        pathParts.splice(1, 0, languageCode);
    }

    window.location.href = pathParts.join('/');
}
</script>

<template>
    <header
        class="flex h-16 shrink-0 items-center gap-2 border-b border-sidebar-border/70 px-6 transition-[width,height] ease-linear group-has-data-[collapsible=icon]/sidebar-wrapper:h-12 md:px-4"
    >
        <div class="flex flex-1 items-center gap-2">
            <SidebarTrigger class="-ml-1" />
            <template v-if="breadcrumbs && breadcrumbs.length > 0">
                <Breadcrumbs :breadcrumbs="breadcrumbs" />
            </template>
        </div>
        <div class="ml-auto flex items-center gap-2">
            <DropdownMenu>
                <DropdownMenuTrigger :as-child="true">
                    <Button
                        variant="ghost"
                        size="icon"
                        class="group h-9 w-9 cursor-pointer"
                    >
                        <Globe class="size-5 opacity-80 group-hover:opacity-100" />
                    </Button>
                </DropdownMenuTrigger>
                <DropdownMenuContent align="end" class="w-40">
                    <DropdownMenuRadioGroup
                        :value="locale"
                        @update:model-value="switchLanguage"
                    >
                        <DropdownMenuRadioItem
                            v-for="language in languages"
                            :key="language.code"
                            :value="language.code"
                        >
                            {{ language.name }}
                        </DropdownMenuRadioItem>
                    </DropdownMenuRadioGroup>
                </DropdownMenuContent>
            </DropdownMenu>
            <DropdownMenu>
                <DropdownMenuTrigger :as-child="true">
                    <Button
                        variant="ghost"
                        size="icon"
                        class="group h-9 w-9 cursor-pointer"
                    >
                        <component
                            :is="themeIcon"
                            class="size-5 opacity-80 group-hover:opacity-100"
                        />
                    </Button>
                </DropdownMenuTrigger>
                <DropdownMenuContent align="end" class="w-40">
                    <DropdownMenuRadioGroup
                        :value="appearance"
                        @update:model-value="(value: string) => updateAppearance(value as 'light' | 'dark' | 'system')"
                    >
                        <DropdownMenuRadioItem value="light">
                            {{ useTrans('appearance.light') }}
                        </DropdownMenuRadioItem>
                        <DropdownMenuRadioItem value="dark">
                            {{ useTrans('appearance.dark') }}
                        </DropdownMenuRadioItem>
                        <DropdownMenuRadioItem value="system">
                            {{ useTrans('appearance.system') }}
                        </DropdownMenuRadioItem>
                    </DropdownMenuRadioGroup>
                </DropdownMenuContent>
            </DropdownMenu>
        </div>
    </header>
</template>
