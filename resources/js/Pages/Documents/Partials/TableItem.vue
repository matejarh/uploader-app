<script setup>
import { ref } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import {
    ArchiveBoxIcon,
    ArrowDownTrayIcon,
    ArrowUturnLeftIcon,
    EyeIcon,
    TrashIcon,
} from '@heroicons/vue/24/solid';
import DialogModal from '@/Components/DialogModal.vue';
import DangerButton from '@/Components/DangerButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import Checkbox from '@/Components/Checkbox.vue';
import Tooltip from '@/Components/Tooltip.vue';

const props = defineProps({
    item: Object,
    loadUser: Boolean,
    archived: Boolean,
    currentUserId: Number,
    canDeleteAnyDocuments: Boolean,
    canDeleteOwnDocuments: Boolean,
    canArchiveAnyDocuments: Boolean,
    canArchiveOwnDocuments: Boolean,
    canRestoreAnyDocuments: Boolean,
    canRestoreOwnDocuments: Boolean,
});

const confirmingDocumentDeletion = ref(false);
const confirmingArchiveChange = ref(false);
const documentToDelete = ref(null);
const searchPhrases = ref([]);

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

const toggleProcessed = () => {
    form.put(route('documents.update', props.item.key), {
        onSuccess: () => {
            // item.processed = !item.processed;
        },
    });
};

const submitArchiveChange = () => {
    form.post(route(props.item.archived ? 'documents.restore' : 'documents.archive', props.item.key), {
        onSuccess: () => {
            confirmingArchiveChange.value = false;
        },
    });
};

const canDeleteDocument = () => props.canDeleteAnyDocuments
    || (props.canDeleteOwnDocuments && props.item.user_id === props.currentUserId);

const canArchiveDocument = () => props.canArchiveAnyDocuments
    || (props.canArchiveOwnDocuments && props.item.user_id === props.currentUserId);

const canRestoreDocument = () => props.canRestoreAnyDocuments
    || (props.canRestoreOwnDocuments && props.item.user_id === props.currentUserId);

const searchDocuments = (phrase, event) => {
    if (event.ctrlKey) {
        // Add the phrase to the searchPhrases array if Ctrl is held
        if (!searchPhrases.value.includes(phrase)) {
            searchPhrases.value.push(phrase);
        }
    } else {
        // If Ctrl is not held, reset the searchPhrases array
        searchPhrases.value = [phrase];
    }

    // Join the phrases with "+" and perform the search
    const query = searchPhrases.value.join('+');
    if (!event.ctrlKey) {
        router.get(route(props.archived ? 'documents.archive.index' : 'documents.index'), { najdi: query });
    } else {
        window.addEventListener('keyup', (e) => {
            if (e.key === 'Control') {
                router.get(route(props.archived ? 'documents.archive.index' : 'documents.index'), { najdi: query });
            }
        }, { once: true });
    }
};

</script>

<template>
    <tr class="border-b dark:border-gray-700 overflow-visible cursor-pointer" >
        <th scope="row" class="px-4 py-3 font-medium text-gray-900 whitespace-nowrap dark:text-white">{{ item.formated_date }}</th>
        <td class="px-4 py-3 cursor-pointer" @click="searchDocuments(item.user.name, $event)" v-if="$page.props.isAdminOrSuperAdmin">{{ item.user.name }}</td>
        <td class="px-4 py-3 cursor-pointer" @click="searchDocuments(item.company.company_name, $event)">{{ item.company.company_name }}</td>
        <td class="px-4 py-3 cursor-pointer" @click="searchDocuments(item.year, $event)">{{ item.year }}</td>
        <td class="px-4 py-3 cursor-pointer" @click="searchDocuments(item.folder, $event)">{{ item.folder }}</td>
        <td class="px-4 py-3 cursor-pointer">
            <a :href="route('documents.download', item.key)" class="text-blue-500 hover:underline">{{ item.file_name }}</a>
        </td>
        <td class="px-4 py-3" v-if="$page.props.isAdminOrSuperAdmin">
            <Checkbox :checked="item.processed" @click="toggleProcessed" />
        </td>
        <td class="px-4 py-3">
            <div class="flex items-center justify-end gap-2 overflow-visible">
                <Tooltip :text="__('View Document')">
                    <a :href="route('documents.show', item.key)" :aria-label="__('View Document')" class="text-gray-500 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-100">
                        <EyeIcon class="w-5 h-5" />
                    </a>
                </Tooltip>
                <Tooltip :text="__('Download Document')">
                    <a :href="route('documents.download', item.key)" :aria-label="__('Download Document')" class="text-gray-500 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-100">
                        <ArrowDownTrayIcon class="w-5 h-5" />
                    </a>
                </Tooltip>
                <Tooltip v-if="!item.archived && canArchiveDocument()" :text="__('Archive Document')">
                    <button type="button" :aria-label="__('Archive Document')" :disabled="form.processing" class="text-gray-500 hover:text-gray-800 disabled:opacity-50 dark:text-gray-400 dark:hover:text-gray-100" @click.stop="confirmingArchiveChange = true">
                        <ArchiveBoxIcon class="w-5 h-5" />
                    </button>
                </Tooltip>
                <Tooltip v-if="item.archived && canRestoreDocument()" :text="__('Restore Document')">
                    <button type="button" :aria-label="__('Restore Document')" :disabled="form.processing" class="text-gray-500 hover:text-gray-800 disabled:opacity-50 dark:text-gray-400 dark:hover:text-gray-100" @click.stop="confirmingArchiveChange = true">
                        <ArrowUturnLeftIcon class="w-5 h-5" />
                    </button>
                </Tooltip>
                <Tooltip v-if="canDeleteDocument()" :text="__('Delete Document')">
                    <button @click.stop="confirmDocumentDeletion(item)" :aria-label="__('Delete Document')" class="text-gray-500 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-100" type="button">
                        <TrashIcon class="w-5 h-5" />
                    </button>
                </Tooltip>
            </div>
        </td>
    </tr>

    <DialogModal :show="confirmingArchiveChange" @close="confirmingArchiveChange = false">
        <template #title>
            {{ item.archived ? __('Restore Document') : __('Archive Document') }}
        </template>

        <template #content>
            {{ item.archived ? __('Are you sure you want to restore this document?') : __('Are you sure you want to archive this document?') }}
        </template>

        <template #footer>
            <SecondaryButton @click="confirmingArchiveChange = false">
                {{ __('Cancel') }}
            </SecondaryButton>

            <SecondaryButton class="ms-3" :disabled="form.processing" @click="submitArchiveChange">
                {{ item.archived ? __('Restore') : __('Archive Document') }}
            </SecondaryButton>
        </template>
    </DialogModal>

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
