<script setup lang="ts">
import { Head, usePage, useForm, router } from '@inertiajs/vue3';
import { computed } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, type Language } from '@/types';
import Form, { type LanguageForm } from '@/components/admin/languages/Form.vue';

interface Props {
    language: Language;
}

const props = defineProps<Props>();
const page = usePage();
const locale = computed(() => page.props.locale as string);
const dashboardUrl = computed(() => `/${locale.value}/dashboard`);

function handleSubmit(form: ReturnType<typeof useForm<LanguageForm>>) {
    form.put(`/${locale.value}/admin/languages/${props.language.id}`, {
        onSuccess: () => {
            router.visit(`/${locale.value}/admin/languages`);
        },
    });
}

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Edit Language',
        href: dashboardUrl.value,
    },
];
</script>

<template>
    <Head title="Edit Language" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <Form
            class="p-2"
            :model="props.language"
            :onSubmit="handleSubmit"
            submitLabel="Update Language"
        />
    </AppLayout>
</template>
