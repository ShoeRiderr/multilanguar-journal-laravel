<script setup lang="ts">
import { useTrans } from '@//composables/trans';
import Input from '@/components/ui/input/Input.vue';
import Label from '@/components/ui/label/Label.vue';
import InputError from '@/components/InputError.vue';
import Checkbox from '@/components/ui/checkbox/Checkbox.vue';
import { Language, Category } from '@/types';
import DropdownMenu from '@/components/ui/dropdown-menu/DropdownMenu.vue';
import DropdownMenuTrigger from '@/components/ui/dropdown-menu/DropdownMenuTrigger.vue';
import DropdownMenuContent from '@/components/ui/dropdown-menu/DropdownMenuContent.vue';
import DropdownMenuCheckboxItem from '@/components/ui/dropdown-menu/DropdownMenuCheckboxItem.vue';
import { computed, ref, type ComponentPublicInstance, onMounted, nextTick } from 'vue';
import { usePage } from '@inertiajs/vue3';

interface Props {
    languages?: Language[];
    categories?: Category[];
    filters?: {
        search?: string;
        categories?: number[];
        languages?: number[];
        date_from?: string;
        date_to?: string;
    };
}

const props = withDefaults(
    defineProps<Props>(),
    {
        languages: () => [],
        categories: () => [],
        filters: () => ({
            search: '',
            categories: [],
            languages: [],
            date_from: '',
            date_to: '',
        }),
    },
);

const page = usePage();
const locale = computed(() => page.props.locale as string);
const defaultLanguageId = computed(() => props.languages.find((lang) => lang.code === locale.value)?.id);

const selectedCategories = ref<number[]>(props.filters.categories ?? []);
const selectedLanguages = ref<number[]>([]);
const search = ref(props.filters.search ?? '');
const dateFrom = ref(props.filters.date_from ?? '');
const dateTo = ref(props.filters.date_to ?? '');

const checkboxRefs = ref<Record<number, InstanceType<typeof Checkbox> | null>>({});
function setCheckboxRef(id: number) {
    return (el: Element | ComponentPublicInstance | null) => {
        checkboxRefs.value[id] = (el && '$el' in el) ? (el as InstanceType<typeof Checkbox>) : null;
    };
}

function triggerLanguageCheckboxClick(id: number) {
    checkboxRefs.value[id]?.$el?.click();
}

onMounted(async () => {
    const initialLanguageIds = (props.filters.languages && props.filters.languages.length > 0)
        ? props.filters.languages
        : (defaultLanguageId.value ? [defaultLanguageId.value] : []);
    if (initialLanguageIds.length === 0) {
        return;
    }
    await nextTick();
    initialLanguageIds.forEach((id) => triggerLanguageCheckboxClick(id));
});

function toggleCategory(id: number) {
    const idx = selectedCategories.value.indexOf(id);
    if (idx === -1) {
        selectedCategories.value.push(id);
    } else {
        selectedCategories.value.splice(idx, 1);
    }
}

function toggleLanguage(id: number, checked: boolean) {
    const idx = selectedLanguages.value.indexOf(id);
    if (checked && idx === -1) {
        selectedLanguages.value.push(id);
        return;
    }
    if (!checked && idx !== -1) {
        selectedLanguages.value.splice(idx, 1);
    }
}

function handleReset() {
    selectedCategories.value = [];
    const languageIdsToReset = [...selectedLanguages.value];
    languageIdsToReset.forEach((id) => triggerLanguageCheckboxClick(id));
    search.value = '';
    dateFrom.value = '';
    dateTo.value = '';
}
</script>

<template>
    <aside class="w-full lg:w-72 flex-shrink-0">
        <form class="sticky top-24 space-y-8" method="GET" :action="`/${locale}/posts`" @reset.prevent="handleReset">
            <div class="space-y-4">
                <h2 class="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                    {{ useTrans('posts.filter.title') }}
                </h2>
                <div class="relative">
                    <div class="grid gap-2">
                        <Label for="search" class="sr-only">Search</Label>
                        <Input
                            class="w-full pr-4 py-2.5 rounded-lg border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:placeholder-slate-500 focus:ring-primary focus:border-primary"
                            id="search" type="text" name="search" ref="searchInput" v-model="search"
                            :placeholder="useTrans('posts.filter.search_keyword')" />
                        <!-- <InputError :message="errors.search" /> -->
                    </div>
                </div>
            </div>
            <div class="space-y-4">
                <h2 class="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                    {{ useTrans('posts.filter.category') }}
                </h2>
                <DropdownMenu>
                    <DropdownMenuTrigger as-child>
                        <button type="button"
                            class="w-full rounded-lg border-slate-200 dark:border-slate-700 dark:bg-slate-800 focus:ring-primary focus:border-primary hover:cursor-pointer px-4 py-2.5 text-left">
                            <span v-if="selectedCategories.length === 0">{{ useTrans('posts.filter.all_categories')
                                }}</span>
                            <span v-else>
                                {{categories.filter(cat => selectedCategories.includes(cat.id)).map(cat =>
                                cat.name).join(', ') }}
                            </span>
                        </button>
                    </DropdownMenuTrigger>
                    <DropdownMenuContent class="w-56">
                        <DropdownMenuCheckboxItem
                            class="data-[state=checked]:bg-primary data-[state=checked]:text-white dark:data-[state=checked]:bg-slate-900 dark:data-[state=checked]:text-white dark:data-[state=checked]:font-bold"
                            :checked="selectedCategories.length === 0"
                            @click="selectedCategories.length = 0">
                            {{ useTrans('posts.filter.all_categories') }}
                        </DropdownMenuCheckboxItem>
                        <DropdownMenuCheckboxItem v-for="category in categories" :key="category.id"
                            class="data-[state=checked]:bg-primary data-[state=checked]:text-white dark:data-[state=checked]:bg-slate-900 dark:data-[state=checked]:text-white dark:data-[state=checked]:font-bold"
                            :checked="selectedCategories.includes(category.id)" @click="toggleCategory(category.id)">
                            {{ category.name }}
                        </DropdownMenuCheckboxItem>
                    </DropdownMenuContent>
                </DropdownMenu>
            </div>
            <div class="space-y-4">
                <h2 class="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                    {{ useTrans('posts.filter.language') }}
                </h2>
                <div class="space-y-2">
                    <Label v-for="language in languages" :key="language.id"
                        :for="`language-${language.id}`"
                        class="flex items-center gap-2 cursor-pointer group">
                        <Checkbox
                            :id="`language-${language.id}`"
                            :checked="selectedLanguages.includes(language.id)"
                            @click="toggleLanguage(language.id, !selectedLanguages.includes(language.id))"
                            :ref="setCheckboxRef(language.id)"
                            class="border-slate-300 dark:border-slate-700 text-primary focus:ring-primary dark:bg-slate-800"
                        />
                        <span
                            class="text-sm text-slate-600 dark:text-slate-300 group-hover:text-primary transition-colors">{{
                            language.name }}</span>
                    </Label>
                </div>
            </div>
            <div class="space-y-4">
                <h2 class="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                    {{ useTrans('posts.filter.date_range.title') }}
                </h2>
                <div class="space-y-3">
                    <div class="relative">
                        <label
                            class="text-[10px] absolute -top-2 left-2 px-1 bg-white dark:bg-slate-900 text-slate-400 font-medium uppercase">{{
                                useTrans('posts.filter.date_range.from') }}</label>
                        <Input
                            class="w-full rounded-lg border-slate-200 dark:border-slate-700 dark:bg-slate-800 text-sm focus:ring-primary focus:border-primary hover:cursor-text"
                            type="date" name="date_from" v-model="dateFrom" />
                    </div>
                    <div class="relative">
                        <label
                            class="text-[10px] absolute -top-2 left-2 px-1 bg-white dark:bg-slate-900 text-slate-400 font-medium uppercase">{{
                                useTrans('posts.filter.date_range.to') }}</label>
                        <Input
                            class="w-full rounded-lg border-slate-200 dark:border-slate-700 dark:bg-slate-800 text-sm focus:ring-primary focus:border-primary hover:cursor-text"
                            type="date" name="date_to" v-model="dateTo" />
                    </div>
                </div>
            </div>
            <input
                v-for="categoryId in selectedCategories"
                :key="`category-${categoryId}`"
                type="hidden"
                name="categories[]"
                :value="categoryId"
            />
            <input
                v-for="languageId in selectedLanguages"
                :key="`language-${languageId}`"
                type="hidden"
                name="languages[]"
                :value="languageId"
            />
            <button
                type="submit"
                class="w-full py-2.5 bg-primary hover:bg-blue-600 text-white font-medium rounded-lg shadow-sm shadow-blue-200 dark:shadow-none transition-all hover:cursor-pointer">
                {{ useTrans('posts.filter.apply') }}
            </button>
            <button
                type="reset"
                class="w-full py-2 text-sm text-slate-500 hover:text-slate-800 dark:hover:text-slate-200 transition-colors underline-offset-4 hover:underline hover:cursor-pointer">
                {{ useTrans('posts.filter.reset') }}
            </button>
        </form>
    </aside>
</template>