
<script lang="ts" setup>
import { useForm, router, usePage } from '@inertiajs/vue3'
import { computed } from 'vue'
import Form from '@/components/admin/pages/Form.vue'

import type { PageForm } from '@/components/admin/pages/Form.vue';

const page = usePage();
const locale = computed(() => page.props.locale as string);

function handleSubmit(form: ReturnType<typeof useForm<PageForm>>) {
  form.post(`/${locale.value}/admin/posts`, {
    onSuccess: () => {
      router.visit(`/${locale.value}/admin/posts`)
    },
    onError: () => {
      // Optionally handle errors
    }
  })
}
</script>


<template>
  <Form :onSubmit="handleSubmit" submitLabel="Create Post" />
</template>

<style scoped>
/* ...existing code... */
</style>
