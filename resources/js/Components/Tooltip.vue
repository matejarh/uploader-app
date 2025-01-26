<script setup>
import ApearDisapearFadeTransition from '@/Transitions/ApearDisapearFadeTransition.vue';
import { computed, ref } from 'vue';
const props = defineProps({
    text: String,
    location: {
        default: 'top',
        type: String,
    }
})

const aligmentClasses = computed(() => {
    return {
        'top': '-top-12 ',
        'bottom': '-bottom-12 ',
        'left': '-left-12',
        'right': '-right-12',
    }[props.location];
})
const arrowAligmentClasses = computed(() => {
    return {
        'top': '-bottom-1',
        'bottom': '-top-1',
        'left': '-right-1',
        'right': '-left-1',
    }[props.location];
})
const show = ref(false)
</script>
<template>
    <div @mouseover="show = true" @mouseleave="show = false">
        <div class="relative select-none cursor-pointer">
            <slot />
            <ApearDisapearFadeTransition>
                <div v-show="show"
                    :class="aligmentClasses"
                    class="inline-block absolute whitespace-nowrap left-1/2 transform -translate-x-1/2 z-50 py-2 px-3 text-sm font-medium text-white bg-gray-900 rounded-lg shadow-sm transition-opacity duration-300"
                    >
                    <div class="z-10">
                        {{ text }}

                    </div>
                    <div :class="arrowAligmentClasses" class="absolute w-3 z-0 h-3 rotate-45 bg-gray-900 left-1/2 -translate-x-1/2  text-lg" />
                </div>

            </ApearDisapearFadeTransition>
        </div>
    </div>

</template>
