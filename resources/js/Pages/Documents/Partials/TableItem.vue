<script setup>
import { ref } from 'vue';
import { usePage, useForm } from '@inertiajs/vue3';
import { TrashIcon } from '@heroicons/vue/24/solid';
import DialogModal from '@/Components/DialogModal.vue';
import DangerButton from '@/Components/DangerButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

const { props } = usePage();

const confirmingDocumentDeletion = ref(false);
const documentToDelete = ref(null);

const form = useForm({});

const confirmDocumentDeletion = (document) => {
    documentToDelete.value = document;
    confirmingDocumentDeletion.value = true;
};

const deleteDocument = () => {
    if (documentToDelete.value) {
        form.delete(route('documents.destroy', documentToDelete.value.key), {
            onSuccess: () => closeModal(),
            onError: () => closeModal(),
        });
    }
};

const closeModal = () => {
    confirmingDocumentDeletion.value = false;
    documentToDelete.value = null;
};

defineProps({
    item: Object,
});

defineEmits(['update']);
</script>

<template>
    <tr class="border-b dark:border-gray-700 overflow-visible cursor-pointer" @click="$emit('update', item)">
        <th scope="row" class="px-4 py-3 font-medium text-gray-900 whitespace-nowrap dark:text-white">{{ item.human_readable_created_at }}</th>
        <td class="px-4 py-3">{{ item.user.name }}</td>
        <td class="px-4 py-3">{{ item.folder }}</td>
        <td class="px-4 py-3">
            <a :href="route('documents.download', item)" class="text-blue-500 hover:underline">{{ item.file_name }}</a>
        </td>
        <td class="px-4 py-3 flex items-center justify-end overflow-visible">
            <button @click.stop="confirmDocumentDeletion(item)" class="inline-flex items-center p-0.5 text-sm font-medium text-center text-gray-500 hover:text-gray-800 rounded-lg focus:outline-none dark:text-gray-400 dark:hover:text-gray-100" type="button">
                <TrashIcon class="w-5 h-5" />
            </button>
        </td>
    </tr>

    <!-- Delete Document Confirmation Modal -->
    <DialogModal :show="confirmingDocumentDeletion" @close="closeModal">
        <template #title>
            {{ __('Delete Document') }}
        </template>

        <template #content>
            {{ __('Are you sure you want to delete this document? Once the document is deleted, all of its resources and data will be permanently deleted.') }}
        </template>

        <template #footer>
            <SecondaryButton @click="closeModal">
                {{ __('Cancel') }}
            </SecondaryButton>

            <DangerButton
                class="ms-3"
                :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing"
                @click="deleteDocument"
            >
                {{ __('Delete Document') }}
            </DangerButton>
        </template>
    </DialogModal>
</template>
