<script setup>
import { usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';


const { props } = usePage();
const document = ref(props.document);
</script>

<template>
    <AppLayout :title="__('Document')">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Document') }}
            </h2>
        </template>

        <div>
            <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                <div>
                    <h1>{{ document.file_name }}</h1>
                    <p><a :href="document.file_url" target="_blank">{{ __('Download') }}</a></p>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
