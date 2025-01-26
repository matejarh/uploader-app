<script setup>
import CheckIcon from '@/Icons/CheckIcon.vue';
import ErrorIcon from '@/Icons/ErrorIcon.vue';
import WarningIcon from '@/Icons/WarningIcon.vue';
import InfoIcon from '@/Icons/InfoIcon.vue';
import CloseIcon from '@/Icons/CloseIcon.vue';
import { computed, ref } from 'vue';
import 'animate.css';

const props = defineProps({
    type: {
        default: 'success', //success|danger|warning|info
        type: String
    },
    text: String
})

const show = ref(true)

const colorClasses = computed(() => {
    switch (props.type) {
        case 'success':
            return 'text-green-500 bg-green-100 rounded-lg dark:bg-green-800 dark:text-green-200'
            break;
        case 'warning':
            return 'text-orange-500 bg-orange-100 rounded-lg dark:bg-orange-800 dark:text-orange-200'
            break;
        case 'error':
            return 'text-red-500 bg-red-100 rounded-lg dark:bg-red-800 dark:text-red-200'
            break;
        case 'info':
            return 'text-blue-500 bg-blue-100 rounded-lg dark:bg-blue-800 dark:text-blue-200'
            break;

        default:
            return 'text-gray-500 bg-gray-100 rounded-lg dark:bg-gray-800 dark:text-gray-200'
            break;
    }
})
</script>

<template>

    <Teleport to="body">
        <Transition
            enter-active-class="animated  bounceInLeft"

            leave-active-class="animated bounceOutRight"

            >
            <div id="toast-success" v-if="show"
                class="absolute top-5 right-5 z-50 flex items-center w-full max-w-xs p-4 mb-4 text-gray-500 bg-gray-200 rounded-lg shadow dark:text-gray-400 dark:bg-gray-900"
                role="alert">
                <div :class="colorClasses"
                    class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 text-green-500 bg-green-100 rounded-lg dark:bg-green-800 dark:text-green-200">
                    <CheckIcon class="w-5 h-5" v-if="type === 'success'" />
                    <span class="sr-only" v-if="type === 'success'">Check icon</span>
                    <ErrorIcon class="w-5 h-5" v-if="type === 'error'" />
                    <span class="sr-only" v-if="type === 'error'">Error icon</span>
                    <WarningIcon class="w-5 h-5" v-if="type === 'warning'" />
                    <span class="sr-only" v-if="type === 'warning'">Warning icon</span>
                    <InfoIcon class="w-5 h-5" v-if="type === 'info'" />
                    <span class="sr-only" v-if="type === 'info'">Info icon</span>
                </div>
                <div class="ms-3 text-sm font-normal">
                    {{ text }}
                </div>
                <button type="button" @click="show = false"
                    class="ms-auto -mx-1.5 -my-1.5 bg-white text-gray-400 hover:text-gray-900 rounded-lg focus:ring-2 focus:ring-gray-300 p-1.5 hover:bg-gray-100 inline-flex items-center justify-center h-8 w-8 dark:text-gray-500 dark:hover:text-white dark:bg-gray-800 dark:hover:bg-gray-700"
                    data-dismiss-target="#toast-success" aria-label="Close">
                    <span class="sr-only">Close</span>
                    <CloseIcon class="w-3 h-3" />

                </button>
            </div>
        </Transition>
    </Teleport>
</template>
