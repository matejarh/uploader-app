<script setup>
import ApearDisapearFadeTransition from '@/Transitions/ApearDisapearFadeTransition.vue';
import { computed, nextTick, onBeforeUnmount, ref } from 'vue';
const props = defineProps({
    text: String,
    location: {
        default: 'top',
        type: String,
    }
})

const trigger = ref(null);
const tooltip = ref(null);
const show = ref(false);
const position = ref({ top: 0, left: 0 });
const arrowPosition = ref({ left: '50%', top: '50%' });
const activeLocation = ref(props.location);
let hideTimeout;

const arrowAlignmentClasses = computed(() => ({
    top: '-bottom-1 -translate-x-1/2',
    bottom: '-top-1 -translate-x-1/2',
    left: '-right-1 -translate-y-1/2',
    right: '-left-1 -translate-y-1/2',
}[activeLocation.value]));

const updatePosition = () => {
    if (!trigger.value || !tooltip.value || typeof window === 'undefined') return;

    const triggerRect = trigger.value.getBoundingClientRect();
    const tooltipRect = tooltip.value.getBoundingClientRect();
    const gap = 8;
    const edge = 8;
    const centerX = triggerRect.left + triggerRect.width / 2;
    const centerY = triggerRect.top + triggerRect.height / 2;
    const preferredLocation = props.location;
    let top;
    let left;

    activeLocation.value = preferredLocation;

    if (preferredLocation === 'bottom') {
        top = triggerRect.bottom + gap;
        left = centerX - tooltipRect.width / 2;
    } else if (preferredLocation === 'left') {
        top = centerY - tooltipRect.height / 2;
        left = triggerRect.left - tooltipRect.width - gap;
    } else if (preferredLocation === 'right') {
        top = centerY - tooltipRect.height / 2;
        left = triggerRect.right + gap;
    } else {
        top = triggerRect.top - tooltipRect.height - gap;
        left = centerX - tooltipRect.width / 2;
    }

    if (preferredLocation === 'top' && top < edge) {
        activeLocation.value = 'bottom';
        top = triggerRect.bottom + gap;
    } else if (preferredLocation === 'bottom' && top + tooltipRect.height > window.innerHeight - edge) {
        activeLocation.value = 'top';
        top = triggerRect.top - tooltipRect.height - gap;
    } else if (preferredLocation === 'left' && left < edge) {
        activeLocation.value = 'right';
        left = triggerRect.right + gap;
    } else if (preferredLocation === 'right' && left + tooltipRect.width > window.innerWidth - edge) {
        activeLocation.value = 'left';
        left = triggerRect.left - tooltipRect.width - gap;
    }

    left = Math.max(edge, Math.min(left, window.innerWidth - tooltipRect.width - edge));
    top = Math.max(edge, Math.min(top, window.innerHeight - tooltipRect.height - edge));

    position.value = { top, left };
    arrowPosition.value = activeLocation.value === 'top' || activeLocation.value === 'bottom'
        ? { left: `${Math.max(12, Math.min(centerX - left, tooltipRect.width - 12))}px` }
        : { top: `${Math.max(12, Math.min(centerY - top, tooltipRect.height - 12))}px` };
};

const showTooltip = async () => {
    clearTimeout(hideTimeout);
    show.value = true;
    await nextTick();
    updatePosition();

    if (typeof window !== 'undefined') {
        window.addEventListener('resize', updatePosition);
        window.addEventListener('scroll', updatePosition, true);
    }
};

const hideTooltip = () => {
    clearTimeout(hideTimeout);
    show.value = false;

    if (typeof window !== 'undefined') {
        window.removeEventListener('resize', updatePosition);
        window.removeEventListener('scroll', updatePosition, true);
    }
};

const scheduleHide = () => {
    hideTimeout = window.setTimeout(hideTooltip, 120);
};

onBeforeUnmount(hideTooltip);
</script>
<template>
    <div ref="trigger" class="inline-flex select-none" @mouseenter="showTooltip" @mouseleave="scheduleHide">
        <slot />
    </div>

    <Teleport to="body">
        <ApearDisapearFadeTransition>
            <div
                v-if="show"
                ref="tooltip"
                :style="{ top: `${position.top}px`, left: `${position.left}px` }"
                class="fixed z-[9999] whitespace-nowrap rounded-lg bg-gray-900 px-3 py-2 text-sm font-medium text-white shadow-sm"
                @mouseenter="showTooltip"
                @mouseleave="hideTooltip"
            >
                <div>{{ text }}</div>
                <div
                    :class="arrowAlignmentClasses"
                    :style="arrowPosition"
                    class="absolute h-3 w-3 rotate-45 bg-gray-900"
                />
            </div>
        </ApearDisapearFadeTransition>
    </Teleport>
</template>
