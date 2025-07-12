<script setup>
import { ref } from 'vue';
import { usePage, useForm, router } from '@inertiajs/vue3';
import { TrashIcon, PencilSquareIcon } from '@heroicons/vue/24/solid';
import DialogModal from '@/Components/DialogModal.vue';
import DangerButton from '@/Components/DangerButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import Checkbox from '@/Components/Checkbox.vue';
import UpdateDialog from './UpdateDialog.vue';

/* const { props } = usePage(); */

const props = defineProps({
    item: Object,
    loadUser: Boolean,
});

const confirmingCompanyDeletion = ref(false);
const companyToDelete = ref(null);
const companyToUpdate = ref(null);

const form = useForm({});

const confirmCompanyDeletion = (company) => {
    companyToDelete.value = company;
    confirmingCompanyDeletion.value = true;
};

const showUpdateDialog = (company) => {
    companyToUpdate.value = company;
};

const deleteCompany = () => {
    if (companyToDelete.value) {
        form.delete(route('companies.destroy', companyToDelete.value), {
            onSuccess: () => closeModal(),
            onError: () => closeModal(),
        });
    }
};

const closeModal = () => {
    confirmingCompanyDeletion.value = false;
    companyToDelete.value = null;
};

const update = () => {
    if (companyToUpdate.value) {
        form.put(route('companies.update', props.item.key), {
            onSuccess: () => {
                // item.processed = !item.processed;
            },
        });
    }
};

const showCompanyDocuments = (company) => {
    router.get(route('documents.index'), { najdi: company.company_name });
};


defineEmits(['update']);
</script>

<template>
    <tr class="border-b dark:border-gray-700 overflow-visible">
        <!-- <th scope="row" class="px-4 py-3 font-medium text-gray-900 whitespace-nowrap dark:text-white">{{ item.formated_date }}</th> -->
        <th scope="row" class="flex items-center px-6 py-4 text-gray-900 whitespace-nowrap dark:text-white cursor-pointer" @click="showCompanyDocuments(item)">
            <img class="w-10 h-10 rounded-full" :src="item.logo_photo_url" :alt="item.company_slug + ' logo'">
            <div class="ps-3">
                <div class="text-base font-semibold">{{ item.company_name }}</div>
                <div class="font-normal text-gray-500">{{ item.user.name }}</div>
            </div>
        </th>
        <!-- <td class="px-4 py-3" v-if="loadUser">{{ item.user.name }}</td>
        <td class="px-4 py-3">{{ item.company_name }}</td> -->
        <td class="px-4 py-3">{{ item.company_address }}</td>


        <!-- <td class="px-4 py-3" v-if="$page.props.isAdminOrSuperAdmin">
            <Checkbox :checked="item.processed" @click="toggleProcessed" />
        </td> -->
        <td class="px-4 py-3 flex items-center justify-end overflow-visible">
            <button @click.stop="$emit('update', item)"
                class="inline-flex items-center p-0.5 text-sm font-medium text-center text-gray-500 hover:text-gray-800 rounded-lg focus:outline-none dark:text-gray-400 dark:hover:text-gray-100"
                type="button">
                <PencilSquareIcon class="w-5 h-5" />
            </button>
            <button @click.stop="confirmCompanyDeletion(item)"
                class="inline-flex items-center p-0.5 text-sm font-medium text-center text-gray-500 hover:text-gray-800 rounded-lg focus:outline-none dark:text-gray-400 dark:hover:text-gray-100"
                type="button">
                <TrashIcon class="w-5 h-5" />
            </button>
        </td>
    </tr>

    <!-- Delete Company Confirmation Modal -->
    <DialogModal :show="confirmingCompanyDeletion" @close="closeModal">
        <template #title>
            {{ __('Delete Company') }}
        </template>

        <template #content>
            {{ __('Are you sure you want to delete this company? Once the company is deleted, all of its resources and data will be permanently deleted.') }}
        </template>

        <template #footer>
            <SecondaryButton @click="closeModal">
                {{ __('Cancel') }}
            </SecondaryButton>

            <DangerButton class="ms-3" :class="{ 'opacity-25': form.processing }" :disabled="form.processing"
                @click="deleteCompany">
                {{ __('Delete Company') }}
            </DangerButton>
        </template>
    </DialogModal>

</template>
