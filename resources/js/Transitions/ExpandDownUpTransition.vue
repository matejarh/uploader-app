<template>
    <transition
            enter-active-class="transition duration-400 ease-out"
            enter-from-class="scale-y-0"
            enter-to-class="scale-y-100"
            leave-active-class="transition duration-400 ease-in"
            leave-from-class="scale-y-100"
            leave-to-class="scale-y-0"
        >
        <slot />
        </transition>
</template>
