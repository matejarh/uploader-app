<script setup>
import { ref } from 'vue';
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
    <AppLayout :title="__('Documents')">
        <template #header>
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Documents') }}
            </h2>
            <Tooltip :text="__('Upload Document')">
                <SecondaryButton @click="showingUploader = true">
                    <ArrowUpTrayIcon class="w-5 h-5" />
                </SecondaryButton>

            </Tooltip>


        </div>
        </template>

        <div>
            <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                <div class="text-gray-800 dark:text-gray-200 ">
                    <TableList :list="documents" :links="links" :filters="filters" @create="showingUploader = true" />
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
