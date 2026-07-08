<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import Icon from './Icon.vue';

/** サブナビの1項目。<a>/<Link> を component 指定で切り替える。 */
const props = defineProps({
    item: { type: Object, required: true },
});

const active = computed(() => {
    const cur = route().current() || '';
    const key = props.item.matchKey || props.item.route;
    return props.item.match === 'startsWith' ? cur.startsWith(key) : cur === key;
});

const tag = computed(() => (props.item.component === 'Link' ? Link : 'a'));

const href = computed(() => {
    try {
        return route(props.item.route);
    } catch (e) {
        return '#';
    }
});
</script>

<template>
    <component
        :is="tag"
        :href="href"
        class="inline-flex items-center me-2 px-5 py-2.5 rounded-lg border text-sm font-medium transition-colors focus:outline-none focus:ring-4 focus:ring-primary-100"
        :class="
            active
                ? 'bg-primary-600 text-white border-primary-600'
                : 'bg-white text-content border-border hover:bg-surface-sunken'
        "
    >
        <Icon v-if="item.icon" :name="item.icon" size="sm" class="mr-1.5" />{{
            item.label
        }}
    </component>
</template>
