<script setup lang="ts">
const emit = defineEmits<{
    accept: [];
}>();
</script>

<template>
    <div class="fixed bottom-0 left-0 right-0 bg-white border-t-2 border-gray-200 shadow-lg z-50">
        <div class="container mx-auto px-4 sm:px-6 py-3 sm:py-4">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 sm:gap-4">
                <p class="text-xs sm:text-sm text-gray-600">
                    This website uses cookies to improve your experience. We'll assume you're ok with this, but you
                    can opt-out if you wish.
                </p>
                <button
                    class="bg-gray-900 hover:bg-gray-800 text-white px-6 py-2 rounded text-sm font-medium transition-colors whitespace-nowrap w-full sm:w-auto"
                    @click="emit('accept')"
                >
                    Accept
                </button>
            </div>
        </div>
    </div>
</template>
