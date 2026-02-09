<script setup lang="ts">
import { onBeforeUnmount, onMounted, ref, watch } from 'vue';
import { Editor } from '@tiptap/core';
import StarterKit from '@tiptap/starter-kit';
import Link from '@tiptap/extension-link';
import Underline from '@tiptap/extension-underline';
import Image from '@tiptap/extension-image';

const props = withDefaults(
    defineProps<{
        modelValue?: string;
        placeholder?: string;
        disabled?: boolean;
        heightClass?: string;
    }>(),
    {
        modelValue: '',
        placeholder: 'Write something... ',
        disabled: false,
        heightClass: 'min-h-[240px]'
    }
);

const emit = defineEmits<{
    (event: 'update:modelValue', value: string): void;
}>();

const editorElement = ref<HTMLDivElement | null>(null);
let editor: Editor | null = null;

const isActive = (name: string, attrs: Record<string, unknown> = {}) => {
    return editor ? editor.isActive(name, attrs) : false;
};

const toggleBold = () => editor?.chain().focus().toggleBold().run();
const toggleItalic = () => editor?.chain().focus().toggleItalic().run();
const toggleUnderline = () => editor?.chain().focus().toggleUnderline().run();
const toggleStrike = () => editor?.chain().focus().toggleStrike().run();
const toggleHeading = () => editor?.chain().focus().toggleHeading({ level: 2 }).run();
const toggleBlockquote = () => editor?.chain().focus().toggleBlockquote().run();
const toggleBulletList = () => editor?.chain().focus().toggleBulletList().run();
const toggleOrderedList = () => editor?.chain().focus().toggleOrderedList().run();
const undo = () => editor?.chain().focus().undo().run();
const redo = () => editor?.chain().focus().redo().run();

const canUndo = () => (editor ? editor.can().chain().focus().undo().run() : false);
const canRedo = () => (editor ? editor.can().chain().focus().redo().run() : false);

const promptForLink = () => {
    if (!editor) return;
    const previous = editor.getAttributes('link').href;
    const url = window.prompt('Enter URL', previous || 'https://');
    if (url === null) return;
    if (url === '') {
        editor.chain().focus().extendMarkRange('link').unsetLink().run();
        return;
    }
    editor.chain().focus().extendMarkRange('link').setLink({ href: url }).run();
};

const promptForImage = () => {
    if (!editor) return;
    const url = window.prompt('Enter image URL', 'https://');
    if (!url) return;
    editor.chain().focus().setImage({ src: url }).run();
};

onMounted(() => {
    if (!editorElement.value) return;

    editor = new Editor({
        element: editorElement.value,
        extensions: [
            StarterKit,
            Underline,
            Link.configure({
                openOnClick: false,
                autolink: true,
                defaultProtocol: 'https',
            }),
            Image,
        ],
        content: props.modelValue || '<p></p>',
        editorProps: {
            attributes: {
                class: `prose prose-sm max-w-none px-4 py-3 text-slate-700 focus:outline-none dark:prose-invert dark:text-slate-200 ${props.heightClass}`,
            },
        },
        onUpdate({ editor }) {
            emit('update:modelValue', editor.getHTML());
        },
    });

    editor.setEditable(!props.disabled);
});

watch(
    () => props.modelValue,
    (value) => {
        if (!editor || editor.isDestroyed) return;
        const current = editor.getHTML();
        if ((value ?? '') !== current) {
            editor.commands.setContent(value || '<p></p>', false);
        }
    }
);

watch(
    () => props.disabled,
    (disabled) => {
        if (!editor) return;
        editor.setEditable(!disabled);
    }
);

onBeforeUnmount(() => {
    editor?.destroy();
});
</script>

<template>
    <div class="w-full rounded-lg border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-900">
        <div class="flex flex-wrap items-center gap-1 border-b border-slate-200 p-2 dark:border-slate-700">
            <button
                type="button"
                class="rounded px-2 py-1 text-sm font-semibold hover:bg-slate-100 dark:hover:bg-slate-800"
                :class="{ 'bg-slate-100 dark:bg-slate-800': isActive('bold') }"
                :disabled="props.disabled"
                @click="toggleBold"
                aria-label="Bold"
            >
                B
            </button>
            <button
                type="button"
                class="rounded px-2 py-1 text-sm italic hover:bg-slate-100 dark:hover:bg-slate-800"
                :class="{ 'bg-slate-100 dark:bg-slate-800': isActive('italic') }"
                :disabled="props.disabled"
                @click="toggleItalic"
                aria-label="Italic"
            >
                I
            </button>
            <button
                type="button"
                class="rounded px-2 py-1 text-sm underline hover:bg-slate-100 dark:hover:bg-slate-800"
                :class="{ 'bg-slate-100 dark:bg-slate-800': isActive('underline') }"
                :disabled="props.disabled"
                @click="toggleUnderline"
                aria-label="Underline"
            >
                U
            </button>
            <button
                type="button"
                class="rounded px-2 py-1 text-sm line-through hover:bg-slate-100 dark:hover:bg-slate-800"
                :class="{ 'bg-slate-100 dark:bg-slate-800': isActive('strike') }"
                :disabled="props.disabled"
                @click="toggleStrike"
                aria-label="Strikethrough"
            >
                S
            </button>
            <span class="mx-1 h-5 w-px bg-slate-200 dark:bg-slate-700" />
            <button
                type="button"
                class="rounded px-2 py-1 text-sm hover:bg-slate-100 dark:hover:bg-slate-800"
                :class="{ 'bg-slate-100 dark:bg-slate-800': isActive('heading', { level: 2 }) }"
                :disabled="props.disabled"
                @click="toggleHeading"
                aria-label="Heading"
            >
                H2
            </button>
            <button
                type="button"
                class="rounded px-2 py-1 text-sm hover:bg-slate-100 dark:hover:bg-slate-800"
                :class="{ 'bg-slate-100 dark:bg-slate-800': isActive('blockquote') }"
                :disabled="props.disabled"
                @click="toggleBlockquote"
                aria-label="Blockquote"
            >
                “ ”
            </button>
            <button
                type="button"
                class="rounded px-2 py-1 text-sm hover:bg-slate-100 dark:hover:bg-slate-800"
                :class="{ 'bg-slate-100 dark:bg-slate-800': isActive('bulletList') }"
                :disabled="props.disabled"
                @click="toggleBulletList"
                aria-label="Bullet list"
            >
                • List
            </button>
            <button
                type="button"
                class="rounded px-2 py-1 text-sm hover:bg-slate-100 dark:hover:bg-slate-800"
                :class="{ 'bg-slate-100 dark:bg-slate-800': isActive('orderedList') }"
                :disabled="props.disabled"
                @click="toggleOrderedList"
                aria-label="Ordered list"
            >
                1. List
            </button>
            <span class="mx-1 h-5 w-px bg-slate-200 dark:bg-slate-700" />
            <button
                type="button"
                class="rounded px-2 py-1 text-sm hover:bg-slate-100 dark:hover:bg-slate-800"
                :disabled="props.disabled"
                @click="promptForLink"
                aria-label="Insert link"
            >
                Link
            </button>
            <button
                type="button"
                class="rounded px-2 py-1 text-sm hover:bg-slate-100 dark:hover:bg-slate-800"
                :disabled="props.disabled"
                @click="promptForImage"
                aria-label="Insert image"
            >
                Image
            </button>
            <span class="mx-1 h-5 w-px bg-slate-200 dark:bg-slate-700" />
            <button
                type="button"
                class="rounded px-2 py-1 text-sm hover:bg-slate-100 dark:hover:bg-slate-800"
                :disabled="props.disabled || !canUndo()"
                @click="undo"
                aria-label="Undo"
            >
                Undo
            </button>
            <button
                type="button"
                class="rounded px-2 py-1 text-sm hover:bg-slate-100 dark:hover:bg-slate-800"
                :disabled="props.disabled || !canRedo()"
                @click="redo"
                aria-label="Redo"
            >
                Redo
            </button>
        </div>
        <div ref="editorElement" />
    </div>
</template>
