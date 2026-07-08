<script setup>
import InputError from '../InputError.vue';

/**
 * ラベル + コントロール + エラーを束ねるフォーム部品。
 * デフォルトは text input を内蔵。select/textarea 等は slot で差し込む。
 */
defineProps({
    label: { type: String, default: '' },
    error: { type: String, default: '' },
    required: { type: Boolean, default: false },
    modelValue: { type: [String, Number], default: '' },
    type: { type: String, default: 'text' },
    placeholder: { type: String, default: '' },
    id: { type: String, default: '' },
    disabled: { type: Boolean, default: false },
});

defineEmits(['update:modelValue']);
</script>

<template>
    <div>
        <label
            v-if="label"
            :for="id || undefined"
            class="block mb-1 text-sm font-medium text-content"
        >
            {{ label }}<span v-if="required" class="text-error-600 ml-0.5">*</span>
        </label>

        <slot>
            <input
                :id="id || undefined"
                :type="type"
                :value="modelValue"
                :placeholder="placeholder"
                :disabled="disabled"
                class="w-full rounded-md border-border shadow-sm text-sm focus:border-primary-500 focus:ring-primary-500 disabled:bg-surface-sunken disabled:text-content-subtle"
                @input="$emit('update:modelValue', $event.target.value)"
            />
        </slot>

        <InputError v-if="error" :message="error" class="mt-1" />
    </div>
</template>
