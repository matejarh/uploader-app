<script setup>
import { computed, onMounted, ref } from 'vue';
import { usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { FolderIcon, UserIcon, UsersIcon, DocumentDuplicateIcon, RectangleGroupIcon } from '@heroicons/vue/24/outline';
import { ExclamationCircleIcon } from '@heroicons/vue/24/solid';

const { props } = usePage();

const latestDocuments = computed(() => props.latestDocuments);

const isAdminOrSuperAdmin = computed(() => props.isAdminOrSuperAdmin);

const showNoCompanyAlert = ref(false);

onMounted(() => {
    if (!props.auth.user.companies_count) {
        showNoCompanyAlert.value = true;
    }
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
                <div v-show="showNoCompanyAlert" id="alert-1" class="flex items-center p-4 mb-4 text-blue-800 rounded-lg bg-blue-50 dark:bg-gray-800 dark:text-blue-400" role="alert">
                    <!-- <svg class="shrink-0 w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
                    </svg> -->
                    <ExclamationCircleIcon class="shrink-0 w-4 h-4" />
                    <span class="sr-only">Info</span>
                    <div class="ms-3 text-sm font-medium">
                        Z vašim računom še ni povezano nobeno podjetje. Podjetje lahko ustvarite <inertia-link :href="route('companies.index')" class="font-semibold underline hover:no-underline">tukaj</inertia-link>.
                    </div>
                        <button @click.prevent="showNoCompanyAlert = false" type="button" class="ms-auto -mx-1.5 -my-1.5 bg-blue-50 text-blue-500 rounded-lg focus:ring-2 focus:ring-blue-400 p-1.5 hover:bg-blue-200 inline-flex items-center justify-center h-8 w-8 dark:bg-gray-800 dark:text-blue-400 dark:hover:bg-gray-700" data-dismiss-target="#alert-1" aria-label="Close">
                        <span class="sr-only">Skrij</span>
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                        </svg>
                    </button>
                </div>
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
                                    <inertia-link :href="route('companies.index')" class="text-blue-500 dark:text-blue-400 hover:underline flex items-center space-x-2">
                                    <RectangleGroupIcon class="w-4 h-4 text-blue-500 dark:text-blue-400 mr-2" />
                                    {{ __('Companies') }}
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
                                        <a :href="route('documents.download', document)" class="text-blue-500 dark:text-blue-400 text-sm hover:underline font-mono"><span >{{ document.company.company_slug }}</span>/{{ document.year }}/{{ document.folder }}/{{ document.file_name }}</a>
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
