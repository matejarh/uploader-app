<template>
    <transition
            enter-active-class="transition duration-400 ease-out"
            enter-from-class="-translate-y-full"
            enter-to-class="translate-y-0"
            leave-active-class="transition duration-400 ease-in"
            leave-from-class="translate-y-0"
            leave-to-class="-translate-y-full"
        >
        <slot />
        </transition>
</template>
