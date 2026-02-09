<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';

export interface LanguageForm {
    code: string;
    name: string;
    native_name: string;
    is_active: boolean;
    is_default: boolean;
}

const props = defineProps<{
    model?: Partial<LanguageForm>;
    onSubmit: (form: ReturnType<typeof useForm<LanguageForm>>) => void;
    submitLabel?: string;
}>();

const form = useForm<LanguageForm>({
    code: props.model?.code ?? '',
    name: props.model?.name ?? '',
    native_name: props.model?.native_name ?? '',
    is_active: Boolean(props.model?.is_active) ?? true,
    is_default: Boolean(props.model?.is_default) ?? false,
});

const submitForm = () => {
    props.onSubmit(form);
};
</script>

<template>
    <form @submit.prevent="submitForm" class="space-y-6">
        <div class="grid gap-4 md:grid-cols-2">
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-200" for="code">
                    Code
                </label>
                <input
                    id="code"
                    v-model="form.code"
                    type="text"
                    class="mt-2 w-full rounded-md border border-slate-200 bg-white px-3 py-2 text-sm shadow-sm focus:border-primary focus:outline-none dark:border-slate-700 dark:bg-slate-900"
                    required
                />
                <InputError :message="form.errors.code" class="mt-2" />
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-200" for="name">
                    Name
                </label>
                <input
                    id="name"
                    v-model="form.name"
                    type="text"
                    class="mt-2 w-full rounded-md border border-slate-200 bg-white px-3 py-2 text-sm shadow-sm focus:border-primary focus:outline-none dark:border-slate-700 dark:bg-slate-900"
                    required
                />
                <InputError :message="form.errors.name" class="mt-2" />
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-200" for="native_name">
                Native name
            </label>
            <input
                id="native_name"
                v-model="form.native_name"
                type="text"
                class="mt-2 w-full rounded-md border border-slate-200 bg-white px-3 py-2 text-sm shadow-sm focus:border-primary focus:outline-none dark:border-slate-700 dark:bg-slate-900"
                required
            />
            <InputError :message="form.errors.native_name" class="mt-2" />
        </div>

        <div class="flex items-center gap-6">
            <label class="inline-flex items-center gap-2 text-sm text-slate-700 dark:text-slate-200">
                <input v-model="form.is_active" type="checkbox" class="rounded border-slate-300" />
                Active
            </label>
            <label class="inline-flex items-center gap-2 text-sm text-slate-700 dark:text-slate-200">
                <input v-model="form.is_default" type="checkbox" class="rounded border-slate-300" />
                Default
            </label>
        </div>

        <div class="flex items-center justify-end">
            <button
                type="submit"
                class="inline-flex items-center rounded-md bg-primary px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary/90"
                :disabled="form.processing"
            >
                {{ submitLabel || 'Save language' }}
            </button>
        </div>
    </form>
</template>
