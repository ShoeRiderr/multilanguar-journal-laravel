<script setup lang="ts">
import { computed, watch } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import TextEditor from '@/components/admin/TextEditor.vue';
import type { Category, Language, Post } from '@/types';
import { useTrans } from '@/composables/trans';

export interface PostForm {
    language_id: number | '';
    title: string;
    slug: string;
    content_md: string;
    status: string;
    published_at: string;
    main_photo: File | null;
    categories: number[];
}

const props = defineProps<{
    model?: Partial<Post>;
    categories?: Category[];
    onSubmit: (form: ReturnType<typeof useForm<PostForm>>) => void;
    submitLabel?: string;
}>();

const page = usePage();
const languages = computed(() => (page.props.languages as Language[]) || []);

const statusOptions = [
    { label: useTrans('posts.status.draft'), value: 'draft' },
    { label: useTrans('posts.status.published'), value: 'published' },
    { label: useTrans('posts.status.archived'), value: 'archived' },
];

const form = useForm<PostForm>({
    language_id: props.model?.language_id ?? '',
    title: props.model?.title ?? '',
    slug: props.model?.slug ?? '',
    content_md: props.model?.content_md ?? '',
    status: props.model?.status ?? 'draft',
    published_at: props.model?.published_at ?? '',
    main_photo: null,
    categories: props.model?.categories?.data.map((category) => category.id) ?? [],
});

watch(
    () => props.model,
    (newModel) => {
        if (!newModel) return;
        form.language_id = newModel.language_id ?? '';
        form.title = newModel.title ?? '';
        form.slug = newModel.slug ?? '';
        form.content_md = newModel.content_md ?? '';
        form.status = newModel.status ?? 'draft';
        form.published_at = newModel.published_at ?? '';
        form.categories = newModel.categories?.data.map((category) => category.id) ?? [];
    },
    { deep: true }
);

const previewUrl = computed(() => props.model?.main_photo?.url ?? null);
const categoryOptions = computed(() => props.categories ?? []);

const onFileChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    const file = target.files?.[0] ?? null;
    form.main_photo = file;
};

const submitForm = () => {
    props.onSubmit(form);
};
</script>

<template>
    <form @submit.prevent="submitForm" class="space-y-6">
        <div class="grid gap-4 md:grid-cols-2">
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-200" for="language_id">
                    {{ useTrans('admin.language') }}
                </label>
                <select id="language_id" v-model="form.language_id"
                    class="mt-2 w-full rounded-md border border-slate-200 bg-white px-3 py-2 text-sm shadow-sm focus:border-primary focus:outline-none dark:border-slate-700 dark:bg-slate-900"
                    required>
                    <option value="" disabled>{{ useTrans('admin.posts.select_language') }}</option>
                    <option v-for="language in languages" :key="language.id" :value="language.id">
                        {{ language.name }}
                    </option>
                </select>
                <InputError :message="form.errors.language_id" class="mt-2" />
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-200" for="status">
                    {{ useTrans('status') }}
                </label>
                <select id="status" v-model="form.status"
                    class="mt-2 w-full rounded-md border border-slate-200 bg-white px-3 py-2 text-sm shadow-sm focus:border-primary focus:outline-none dark:border-slate-700 dark:bg-slate-900"
                    required>
                    <option v-for="option in statusOptions" :key="option.value" :value="option.value">
                        {{ option.label }}
                    </option>
                </select>
                <InputError :message="form.errors.status" class="mt-2" />
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-200" for="title">Title</label>
            <input id="title" v-model="form.title" type="text"
                class="mt-2 w-full rounded-md border border-slate-200 bg-white px-3 py-2 text-sm shadow-sm focus:border-primary focus:outline-none dark:border-slate-700 dark:bg-slate-900"
                required />
            <InputError :message="form.errors.title" class="mt-2" />
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-200" for="slug">Slug</label>
            <input id="slug" v-model="form.slug" type="text"
                class="mt-2 w-full rounded-md border border-slate-200 bg-white px-3 py-2 text-sm shadow-sm focus:border-primary focus:outline-none dark:border-slate-700 dark:bg-slate-900"
                required />
            <InputError :message="form.errors.slug" class="mt-2" />
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-200">{{ useTrans('admin.content') }}</label>
            <div class="mt-2">
                <TextEditor v-model="form.content_md" />
            </div>
            <InputError :message="form.errors.content_md" class="mt-2" />
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-200">
                {{ useTrans('categories') }}
            </label>
            <div class="mt-2 grid gap-2 md:grid-cols-2">
                <label v-for="category in categoryOptions" :key="category.id"
                    class="flex items-center gap-2 rounded-md border border-slate-200 px-3 py-2 text-sm text-slate-700 dark:border-slate-700 dark:text-slate-200">
                    <input type="checkbox" :value="category.id" v-model="form.categories"
                        class="rounded border-slate-300" />
                    <span>{{ category.name || `${useTrans('category')} #${category.id}` }}</span>
                </label>
            </div>
            <InputError :message="form.errors.categories" class="mt-2" />
        </div>

        <div class="grid gap-4 md:grid-cols-2">
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-200" for="published_at">
                    {{ useTrans('published_at') }}
                </label>
                <input id="published_at" v-model="form.published_at" type="datetime-local"
                    class="mt-2 w-full rounded-md border border-slate-200 bg-white px-3 py-2 text-sm shadow-sm focus:border-primary focus:outline-none dark:border-slate-700 dark:bg-slate-900"
                    required />
                <InputError :message="form.errors.published_at" class="mt-2" />
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-200" for="main_photo">
                    {{useTrans('posts.main_photo')}}
                </label>
                <input id="main_photo" type="file" accept="image/*" class="mt-2 w-full text-sm text-slate-600"
                    @change="onFileChange" />
                <InputError :message="form.errors.main_photo" class="mt-2" />
                <div v-if="previewUrl" class="mt-3">
                    <img :src="previewUrl" alt="Main photo"
                        class="h-32 rounded-md border border-slate-200 object-cover" />
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3">
            <button type="submit"
                class="inline-flex items-center rounded-md bg-primary px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary/90"
                :disabled="form.processing">
                {{ submitLabel || useTrans('admin.posts.save') }}
            </button>
        </div>
    </form>
</template>
