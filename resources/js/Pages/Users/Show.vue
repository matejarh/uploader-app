<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import TableList from '../Documents/Partials/TableList.vue';


defineProps({
    user: Object,
    documents: Object,
    links: String,
    filters: Object,
});
</script>

<template>
    <AppLayout :title="__('User')">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                <!-- {{ __('User') }} -->

                <span class="flex items-center px-6 py-0 text-gray-900 whitespace-nowrap dark:text-white">
                    <img class="w-10 h-10 rounded-full" :src="user.profile_photo_url" :alt="user.name + 's image'">
                    <div class="ps-3">
                        <div class="text-base font-semibold">{{ user.name }}</div>
                        <div class="font-normal text-gray-500">{{ user.email }}</div>
                    </div>
                </span>
            </h2>
        </template>

        <div>
            <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                <div class="text-gray-800 dark:text-gray-200 ">
                    <TableList :list="documents" :links="links" :filters="filters" :load-user="false"/>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
