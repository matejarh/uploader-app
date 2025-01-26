<script setup>
import { ref } from 'vue';
import { usePage, useForm } from '@inertiajs/vue3';
import { TrashIcon } from '@heroicons/vue/24/solid';
import DialogModal from '@/Components/DialogModal.vue';
import DangerButton from '@/Components/DangerButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

const { props } = usePage();

const confirmingUserDeletion = ref(false);
const userToDelete = ref(null);

const form = useForm({});

const confirmUserDeletion = (user) => {
    userToDelete.value = user;
    confirmingUserDeletion.value = true;
};

const deleteUser = () => {
    if (userToDelete.value) {
        form.delete(route('users.destroy', userToDelete.value.id), {
            onSuccess: () => closeModal(),
            onError: () => closeModal(),
        });
    }
};

const closeModal = () => {
    confirmingUserDeletion.value = false;
    userToDelete.value = null;
};

defineProps({
    item: Object,
});

defineEmits(['update']);
</script>

<template>
    <tr class="border-b dark:border-gray-700 overflow-visible cursor-pointer" @click="$emit('update', item)">
        <th scope="row" class="px-4 py-3 font-medium text-gray-900 whitespace-nowrap dark:text-white">{{ item.human_readable_created_at }}</th>
        <td class="px-4 py-3">{{ item.name }}</td>
        <td class="px-4 py-3">
            {{ item.email }}
        </td>
        <td class="px-4 py-3 flex items-center justify-end overflow-visible">
            <button @click.stop="confirmUserDeletion(item)" class="inline-flex items-center p-0.5 text-sm font-medium text-center text-gray-500 hover:text-gray-800 rounded-lg focus:outline-none dark:text-gray-400 dark:hover:text-gray-100" type="button">
                <TrashIcon class="w-5 h-5" />
            </button>
        </td>
    </tr>

    <!-- Delete User Confirmation Modal -->
    <DialogModal :show="confirmingUserDeletion" @close="closeModal">
        <template #title>
            {{ __('Delete User') }}
        </template>

        <template #content>
            {{ __('Are you sure you want to delete this user? Once the user is deleted, all of its resources and data will be permanently deleted.') }}
        </template>

        <template #footer>
            <SecondaryButton @click="closeModal">
                {{ __('Cancel') }}
            </SecondaryButton>

            <DangerButton
                class="ms-3"
                :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing"
                @click="deleteUser"
            >
                {{ __('Delete User') }}
            </DangerButton>
        </template>
    </DialogModal>
</template>
