<script setup lang="ts">
import { computed, watch } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import type { Category, Language } from '@/types';

export interface CategoryTranslationForm {
    language_id: number;
    name: string;
    slug: string;
}

export interface CategoryForm {
    parent_id: number | '';
    translations: CategoryTranslationForm[];
}

const props = defineProps<{
    model?: {
        id?: number;
        parent_id?: number | null;
        translations?: CategoryTranslationForm[];
    };
    categories?: Category[];
    onSubmit: (form: ReturnType<typeof useForm<CategoryForm>>) => void;
    submitLabel?: string;
}>();

const page = usePage();
const languages = computed(() => (page.props.languages as Language[]) || []);

const buildTranslations = () =>
    languages.value.map((language) => {
        const existing = props.model?.translations?.find(
            (translation) => translation.language_id === language.id
        );
        return {
            language_id: language.id,
            name: existing?.name ?? '',
            slug: existing?.slug ?? '',
        };
    });

const form = useForm<CategoryForm>({
    parent_id: props.model?.parent_id ?? '',
    translations: buildTranslations(),
});

watch(
    () => props.model,
    () => {
        form.parent_id = props.model?.parent_id ?? '';
        form.translations = buildTranslations();
    },
    { deep: true }
);

const parentOptions = computed(() => props.categories ?? []);

const submitForm = () => {
    props.onSubmit(form);
};
</script>

<template>
    <form @submit.prevent="submitForm" class="space-y-6">
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-200" for="parent_id">
                Parent category (optional)
            </label>
            <select
                id="parent_id"
                v-model="form.parent_id"
                class="mt-2 w-full rounded-md border border-slate-200 bg-white px-3 py-2 text-sm shadow-sm focus:border-primary focus:outline-none dark:border-slate-700 dark:bg-slate-900"
            >
                <option value="">No parent</option>
                <option v-for="option in parentOptions" :key="option.id" :value="option.id">
                    {{ option.name || `Category #${option.id}` }}
                </option>
            </select>
            <InputError :message="form.errors.parent_id" class="mt-2" />
        </div>

        <div class="space-y-6">
            <div
                v-for="(translation, index) in form.translations"
                :key="translation.language_id"
                class="rounded-lg border border-slate-200 p-4 dark:border-slate-700"
            >
                <div class="mb-4 text-sm font-semibold text-slate-700 dark:text-slate-200">
                    {{ languages.find((lang) => lang.id === translation.language_id)?.name || 'Translation' }}
                </div>
                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-200" :for="`name-${index}`">
                            Name
                        </label>
                        <input
                            :id="`name-${index}`"
                            v-model="translation.name"
                            type="text"
                            class="mt-2 w-full rounded-md border border-slate-200 bg-white px-3 py-2 text-sm shadow-sm focus:border-primary focus:outline-none dark:border-slate-700 dark:bg-slate-900"
                            required
                        />
                        <InputError :message="form.errors[`translations.${index}.name`]" class="mt-2" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-200" :for="`slug-${index}`">
                            Slug
                        </label>
                        <input
                            :id="`slug-${index}`"
                            v-model="translation.slug"
                            type="text"
                            class="mt-2 w-full rounded-md border border-slate-200 bg-white px-3 py-2 text-sm shadow-sm focus:border-primary focus:outline-none dark:border-slate-700 dark:bg-slate-900"
                            required
                        />
                        <InputError :message="form.errors[`translations.${index}.slug`]" class="mt-2" />
                    </div>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3">
            <button
                type="submit"
                class="inline-flex items-center rounded-md bg-primary px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary/90"
                :disabled="form.processing"
            >
                {{ submitLabel || 'Save category' }}
            </button>
        </div>
    </form>
</template>
