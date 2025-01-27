<script setup>
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { FolderIcon, UserIcon, UsersIcon, DocumentDuplicateIcon } from '@heroicons/vue/24/outline';

const { props } = usePage();
const latestDocuments = computed(() => props.latestDocuments);

const isAdminOrSuperAdmin = computed(() => {
    const user = props.auth.user;
    if (user && user.roles) {
        const roles = user.roles.map(role => role.name);
        return roles.includes('Super-Admin') || roles.includes('admin');
    }
    return false;
});
</script>

<template>
    <AppLayout :title="__('Dashboard')">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
               {{ __('Dashboard') }}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 p-6">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">{{ __('Quick Links') }}</h3>
                            <ul class="mt-4">
                                <li>
                                    <inertia-link :href="route('profile.show')" class="text-blue-500 dark:text-blue-400 hover:underline flex items-center space-x-2">
                                    <UserIcon class="w-4 h-4 text-blue-500 dark:text-blue-400 mr-2" />
                                    {{ __('Profile') }}
                                    </inertia-link>
                                </li>
                                <li>
                                    <inertia-link :href="route('documents.index')" class="text-blue-500 dark:text-blue-400 hover:underline flex items-center space-x-2">
                                    <DocumentDuplicateIcon class="w-4 h-4 text-blue-500 dark:text-blue-400 mr-2" />
                                    {{ __('Documents') }}
                                    </inertia-link>
                                </li>
                                <li>
                                    <inertia-link v-if="isAdminOrSuperAdmin" :href="route('users.index')" class="text-blue-500 dark:text-blue-400 hover:underline flex items-center space-x-2">
                                    <UsersIcon class="w-4 h-4 text-blue-500 dark:text-blue-400 mr-2" />
                                    {{ __('Users') }}
                                    </inertia-link>
                                </li>
                            </ul>
                        </div>
                        <div class="col-span-2">
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">{{ __('Latest Uploaded Documents') }}</h3>
                            <ul class="mt-4 whitespace-nowrap overflow-x-auto">
                                <li v-for="document in latestDocuments" :key="document.id" class="">
                                    <div class="flex items-center space-x-2">
                                        <FolderIcon class="w-5 h-5 text-gray-500 dark:text-gray-400" />
                                        <a :href="route('documents.download', document)" class="text-blue-500 dark:text-blue-400 text-sm hover:underline font-mono"><span v-if="isAdminOrSuperAdmin">{{ $page.props.auth.user.email }}</span>/{{ document.folder }}/{{ document.file_name }}</a>
                                        <span class="text-gray-600 dark:text-gray-400 text-xs font-mono">({{ document.human_readable_created_at }})</span>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
