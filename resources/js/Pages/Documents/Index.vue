<script setup>
import { ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { ArrowUpTrayIcon } from '@heroicons/vue/24/solid'
import SecondaryButton from '@/Components/SecondaryButton.vue'
import Tooltip from '@/Components/Tooltip.vue'
import DialogModal from '@/Components/DialogModal.vue'
import FileUploader from '@/Components/FileUploader.vue'
import TableList from './Partials/TableList.vue';

const props = defineProps({
    documents: Object,
    links: String,
    filters: Object,
    archived: {
        type: Boolean,
        default: false,
    },
    currentUserId: Number,
    canDeleteAnyDocuments: Boolean,
    canDeleteOwnDocuments: Boolean,
    canArchiveAnyDocuments: Boolean,
    canArchiveOwnDocuments: Boolean,
    canRestoreAnyDocuments: Boolean,
    canRestoreOwnDocuments: Boolean,
    loadUser: {
        type: Boolean,
        default: true,
    },
})

const showingUploader = ref(false);

const handleFileUploaded = (status) => {
    if (status === true) {
        showingUploader.value = false;
        // alert("File uploaded successfully!");
    } else {
        alert("Nalaganje datoteke je spodletelo");
    }
};
</script>

<template>
    <AppLayout :title="props.archived ? __('Archive') : __('Documents')">
        <template #header>
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ props.archived ? __('Archive') : __('Documents') }} <span v-show="props.filters.najdi">za <span class="italic">{{ props.filters.najdi }}</span></span>
            </h2>
            <Tooltip v-if="!props.archived" :text="__('Upload Document')">
                <SecondaryButton @click="showingUploader = true">
                    <ArrowUpTrayIcon class="w-5 h-5" />
                </SecondaryButton>

            </Tooltip>


        </div>
        </template>

        <div>
            <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                <nav class="mb-6 flex border-b border-gray-200 dark:border-gray-700" aria-label="Documents">
                    <Link
                        :href="route('documents.index')"
                        :aria-current="props.archived ? null : 'page'"
                        class="border-b-2 px-4 py-2 text-sm font-medium"
                        :class="props.archived ? 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200' : 'border-indigo-500 text-indigo-600 dark:text-indigo-400'"
                    >
                        {{ __('Documents') }}
                    </Link>
                    <Link
                        :href="route('documents.archive.index')"
                        :aria-current="props.archived ? 'page' : null"
                        class="border-b-2 px-4 py-2 text-sm font-medium"
                        :class="props.archived ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200'"
                    >
                        {{ __('Archive') }}
                    </Link>
                </nav>
                <div class="text-gray-800 dark:text-gray-200 ">
                    <TableList
                        :list="props.documents"
                        :links="props.links"
                        :load-user="props.loadUser"
                        :filters="props.filters"
                        :archived="props.archived"
                        :current-user-id="props.currentUserId"
                        :can-delete-any-documents="props.canDeleteAnyDocuments"
                        :can-delete-own-documents="props.canDeleteOwnDocuments"
                        :can-archive-any-documents="props.canArchiveAnyDocuments"
                        :can-archive-own-documents="props.canArchiveOwnDocuments"
                        :can-restore-any-documents="props.canRestoreAnyDocuments"
                        :can-restore-own-documents="props.canRestoreOwnDocuments"
                        @create="showingUploader = true"
                    />
                </div>
            </div>
        </div>

        <!-- Uploader Modal -->
        <DialogModal :show="showingUploader" @close="showingUploader = false">
            <template #title>
                {{ __('Upload Document') }}
            </template>

            <template #content>
                <FileUploader @file-uploaded="handleFileUploaded" />
            </template>

            <template #footer>
<!--                 <div class="flex justify-end">
                    <SecondaryButton @click="showingUploader = false">
                        {{ __('Cancel') }}
                    </SecondaryButton>
                    <SecondaryButton @click="showingUploader = false">
                        {{ __('Upload') }}
                    </SecondaryButton>
                </div> -->
            </template>

        </DialogModal>
    </AppLayout>
</template>
