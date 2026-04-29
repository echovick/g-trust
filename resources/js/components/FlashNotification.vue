<script setup lang="ts">
import { ref, watch, computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { X, CheckCircle, AlertCircle, AlertTriangle } from 'lucide-vue-next';

const page = usePage();
const visible = ref(false);
const currentMessage = ref('');
const currentType = ref<'success' | 'error' | 'warning'>('success');
let timer: ReturnType<typeof setTimeout> | null = null;

const flash = computed(() => page.props.flash as { success?: string; error?: string; warning?: string } | null);

const show = (message: string, type: 'success' | 'error' | 'warning') => {
    if (timer) clearTimeout(timer);
    currentMessage.value = message;
    currentType.value = type;
    visible.value = true;
    timer = setTimeout(() => { visible.value = false; }, 5000);
};

const dismiss = () => {
    if (timer) clearTimeout(timer);
    visible.value = false;
};

watch(flash, (f) => {
    if (f?.success) show(f.success, 'success');
    else if (f?.error) show(f.error, 'error');
    else if (f?.warning) show(f.warning, 'warning');
}, { immediate: true, deep: true });

const styles = computed(() => {
    switch (currentType.value) {
        case 'success': return { bg: 'bg-green-50 border-green-400', text: 'text-green-800', icon: CheckCircle, iconClass: 'text-green-500' };
        case 'error':   return { bg: 'bg-red-50 border-red-400',   text: 'text-red-800',   icon: AlertCircle,  iconClass: 'text-red-500' };
        case 'warning': return { bg: 'bg-yellow-50 border-yellow-400', text: 'text-yellow-800', icon: AlertTriangle, iconClass: 'text-yellow-500' };
    }
});
</script>

<template>
    <Transition
        enter-active-class="transform ease-out duration-300 transition"
        enter-from-class="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-4"
        enter-to-class="translate-y-0 opacity-100 sm:translate-x-0"
        leave-active-class="transition ease-in duration-200"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
    >
        <div
            v-if="visible && currentMessage"
            class="fixed top-4 right-4 z-[9999] max-w-sm w-full shadow-lg rounded-lg border pointer-events-auto"
            :class="styles?.bg"
        >
            <div class="p-4 flex items-start gap-3">
                <component :is="styles?.icon" :size="20" class="flex-shrink-0 mt-0.5" :class="styles?.iconClass" />
                <p class="flex-1 text-sm font-medium" :class="styles?.text">{{ currentMessage }}</p>
                <button @click="dismiss" class="flex-shrink-0 ml-1 opacity-60 hover:opacity-100 transition-opacity" :class="styles?.text">
                    <X :size="16" />
                </button>
            </div>
            <!-- Progress bar -->
            <div class="h-1 rounded-b-lg overflow-hidden bg-black/10">
                <div
                    class="h-full origin-left animate-shrink"
                    :class="currentType === 'success' ? 'bg-green-500' : currentType === 'error' ? 'bg-red-500' : 'bg-yellow-500'"
                ></div>
            </div>
        </div>
    </Transition>
</template>

<style scoped>
@keyframes shrink {
    from { transform: scaleX(1); }
    to   { transform: scaleX(0); }
}
.animate-shrink {
    animation: shrink 5s linear forwards;
}
</style>
