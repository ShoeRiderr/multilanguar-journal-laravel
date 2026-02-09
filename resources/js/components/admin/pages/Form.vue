<script lang="ts" setup>
import { computed, watch } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import TextEditor from '@/components/admin/TextEditor.vue';
import type { Language } from '@/types';
import { useTrans } from '@/composables/trans';

export interface PageTranslationForm {
    language_id: number;
    title: string;
    slug: string;
    content_md: string;
}

export interface PageForm {
    is_active: boolean;
    translations: PageTranslationForm[];
}

const props = defineProps<{
    model?: {
        id?: number;
        is_active?: boolean;
        translations?: PageTranslationForm[];
    };
    onSubmit: (form: ReturnType<typeof useForm<PageForm>>) => void;
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
            title: existing?.title ?? '',
            slug: existing?.slug ?? '',
            content_md: existing?.content_md ?? '',
        };
    });

const form = useForm<PageForm>({
    is_active: props.model?.is_active ?? true,
    translations: buildTranslations(),
});

watch(
    () => props.model,
    () => {
        form.is_active = props.model?.is_active ?? true;
        form.translations = buildTranslations();
    },
    { deep: true }
);

const submitForm = () => {
    props.onSubmit(form);
};
</script>

<template>
    <form @submit.prevent="submitForm" class="space-y-6">
        <div>
            <label class="inline-flex items-center gap-2 text-sm text-slate-700 dark:text-slate-200">
                <input v-model="form.is_active" type="checkbox" class="rounded border-slate-300" />
                {{ useTrans('admin.pages.active') }}
            </label>
            <InputError :message="form.errors.is_active" class="mt-2" />
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
                        <label
                            class="block text-sm font-medium text-slate-700 dark:text-slate-200"
                            :for="`title-${index}`"
                        >
                            {{ useTrans('admin.title') }}
                        </label>
                        <input
                            :id="`title-${index}`"
                            v-model="translation.title"
                            type="text"
                            class="mt-2 w-full rounded-md border border-slate-200 bg-white px-3 py-2 text-sm shadow-sm focus:border-primary focus:outline-none dark:border-slate-700 dark:bg-slate-900"
                            required
                        />
                        <InputError :message="form.errors[`translations.${index}.title`]" class="mt-2" />
                    </div>
                    <div>
                        <label
                            class="block text-sm font-medium text-slate-700 dark:text-slate-200"
                            :for="`slug-${index}`"
                        >
                            {{ useTrans('admin.slug') }}
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

                <div class="mt-4">
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-200">{{ useTrans('admin.content') }}</label>
                    <div class="mt-2">
                        <TextEditor v-model="translation.content_md" />
                    </div>
                    <InputError :message="form.errors[`translations.${index}.content_md`]" class="mt-2" />
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end">
            <button
                type="submit"
                class="inline-flex items-center rounded-md bg-primary px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary/90"
                :disabled="form.processing"
            >
                {{ submitLabel || useTrans('admin.pages.save') }}
            </button>
        </div>
    </form>
</template>
