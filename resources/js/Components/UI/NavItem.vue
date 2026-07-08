<script setup>
import { computed } from 'vue';
import Icon from './Icon.vue';

/** ヘッダー主ナビの1項目。濃色ネイビー背景の上に表示される。 */
const props = defineProps({
    item: { type: Object, required: true },
});

const active = computed(() => {
    try {
        return (route().current() || '').includes(props.item.match);
    } catch (e) {
        return false;
    }
});

const href = computed(() => {
    try {
        return route(props.item.route);
    } catch (e) {
        return '#';
    }
});
</script>

<template>
    <a
        :href="href"
        class="flex items-center mt-4 lg:mt-0 mr-2 px-3 py-2 rounded-md text-sm font-medium transition-colors"
        :class="
            active
                ? 'bg-primary-700 text-white font-bold'
                : 'text-primary-100 hover:bg-primary-800 hover:text-white'
        "
    >
        <Icon :name="item.icon" size="sm" class="mr-1.5" />{{ item.label }}
    </a>
</template>
