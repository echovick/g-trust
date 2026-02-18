<script setup lang="ts">
const props = defineProps<{
    columns?: 2 | 3 | 4;
}>();

const gridCols = {
    2: 'md:grid-cols-2',
    3: 'md:grid-cols-2 lg:grid-cols-3',
    4: 'md:grid-cols-2 lg:grid-cols-4',
};
</script>

<template>
    <div :class="['grid grid-cols-1 gap-6', gridCols[columns || 3]]">
        <slot />
    </div>
</template>
