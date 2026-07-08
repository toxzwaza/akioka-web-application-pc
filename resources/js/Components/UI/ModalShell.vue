<script setup>
import Modal from '../Modal.vue';
import Icon from './Icon.vue';

/** 既存 Modal をラップし、ヘッダー / 本文 / フッターの定型を提供する。 */
defineProps({
    show: { type: Boolean, default: false },
    title: { type: String, default: '' },
    maxWidth: { type: String, default: '2xl' },
    closeable: { type: Boolean, default: true },
});

defineEmits(['close']);
</script>

<template>
    <Modal
        :show="show"
        :max-width="maxWidth"
        :closeable="closeable"
        @close="$emit('close')"
    >
        <div
            class="flex items-center justify-between px-6 py-4 border-b border-border"
        >
            <h3 class="text-base font-bold text-content">{{ title }}</h3>
            <button
                v-if="closeable"
                type="button"
                class="text-content-subtle hover:text-content transition-colors"
                aria-label="閉じる"
                @click="$emit('close')"
            >
                <Icon name="close" size="md" />
            </button>
        </div>

        <div class="px-6 py-5"><slot /></div>

        <div
            v-if="$slots.footer"
            class="flex items-center justify-end gap-2 px-6 py-4 border-t border-border bg-surface-muted"
        >
            <slot name="footer" />
        </div>
    </Modal>
</template>
