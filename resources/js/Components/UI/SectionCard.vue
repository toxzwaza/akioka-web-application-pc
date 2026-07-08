<script setup>
import { computed } from 'vue';

/** タイトル付きカード。header / actions / footer スロットを持つ。 */
const props = defineProps({
    title: { type: String, default: '' },
    subtitle: { type: String, default: '' },
    padding: { type: String, default: 'md' }, // none / sm / md / lg
});

const bodyPadding = computed(
    () =>
        ({
            none: '',
            sm: 'p-4',
            md: 'p-6',
            lg: 'p-8',
        }[props.padding])
);
</script>

<template>
    <section
        class="bg-surface-base border border-border rounded-card shadow-card overflow-hidden"
    >
        <header
            v-if="title || $slots.header || $slots.actions"
            class="flex items-center justify-between gap-4 px-6 py-4 border-b border-border"
        >
            <div v-if="title || subtitle">
                <h2 class="text-base font-bold text-content">{{ title }}</h2>
                <p v-if="subtitle" class="mt-0.5 text-sm text-content-muted">
                    {{ subtitle }}
                </p>
            </div>
            <slot name="header" />
            <div v-if="$slots.actions" class="flex items-center gap-2">
                <slot name="actions" />
            </div>
        </header>

        <div :class="bodyPadding"><slot /></div>

        <footer
            v-if="$slots.footer"
            class="px-6 py-4 border-t border-border bg-surface-muted"
        >
            <slot name="footer" />
        </footer>
    </section>
</template>
