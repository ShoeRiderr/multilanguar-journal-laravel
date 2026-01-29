<script lang="ts" setup>
import { useForm, router, usePage } from '@inertiajs/vue3'
import { computed, watch, toRefs } from 'vue'

export interface PageForm {
  language_id: number | ''
  title: string
  slug: string
  content_md: string
  status?: string
  published_at?: string
}

const props = defineProps<{
  model?: Partial<PageForm>
  onSubmit: (form: ReturnType<typeof useForm<PageForm>>) => void
  submitLabel?: string
}>()

const page = usePage();
const locale = computed(() => page.props.locale as string);

const form = useForm<PageForm>({
  language_id: props.model?.language_id ?? '',
  title: props.model?.title ?? '',
  slug: props.model?.slug ?? '',
  content_md: props.model?.content_md ?? '',
  status: props.model?.status ?? '',
  published_at: props.model?.published_at ?? ''
})

watch(() => props.model, (newModel) => {
  if (newModel) {
    form.language_id = newModel.language_id ?? ''
    form.title = newModel.title ?? ''
    form.slug = newModel.slug ?? ''
    form.content_md = newModel.content_md ?? ''
    form.status = newModel.status ?? ''
    form.published_at = newModel.published_at ?? ''
  }
}, { deep: true })

function submitForm() {
  props.onSubmit(form)
}
</script>

<template>
  <form @submit.prevent="submitForm">
    <div>
      <label for="language_id">Language ID</label>
      <input v-model="form.language_id" id="language_id" type="number" required />
    </div>
    <div>
      <label for="title">Title</label>
      <input v-model="form.title" id="title" type="text" required />
    </div>
    <div>
      <label for="slug">Slug</label>
      <input v-model="form.slug" id="slug" type="text" required />
    </div>
    <div>
      <label for="content_md">Content (Markdown)</label>
      <textarea v-model="form.content_md" id="content_md" required></textarea>
    </div>
    <div>
      <label for="status">Status</label>
      <input v-model="form.status" id="status" type="text" required />
    </div>
    <div>
      <label for="published_at">Published At</label>
      <input v-model="form.published_at" id="published_at" type="datetime-local" />
    </div>
    <button type="submit">{{ props.submitLabel || 'Submit' }}</button>
  </form>
</template>

<style scoped>
form {
  display: flex;
  flex-direction: column;
  gap: 1rem;
  max-width: 500px;
}
label {
  font-weight: bold;
}
input, textarea {
  width: 100%;
  padding: 0.5rem;
  font-size: 1rem;
}
button {
  width: fit-content;
  padding: 0.5rem 1rem;
}
</style>
