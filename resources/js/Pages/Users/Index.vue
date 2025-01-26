<script setup>
import { ref, computed } from "vue";
import { usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { UserPlusIcon } from '@heroicons/vue/24/solid';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import Tooltip from '@/Components/Tooltip.vue';
import DialogModal from '@/Components/DialogModal.vue';
import TableList from './Partials/TableList.vue';
import UserRegistrationForm from './Partials/UserRegistrationForm.vue';

const props = defineProps({
    users: Object,
    links: String,
    filters: Object,
});

const { props: pageProps } = usePage();

const showingUserRegistration = ref(false);

const canAddUser = computed(() => {
    const user = pageProps.auth.user;
    if (user && user.roles) {
        const roles = user.roles.map(role => role.name);
        return roles.includes('Super-Admin') || user.permissions.includes('add user');
    }
    return false;
});
</script>

<template>
    <AppLayout :title="__('Users')">
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    {{ __('Users') }}
                </h2>
                <Tooltip v-if="canAddUser" :text="__('Create User')">
                    <SecondaryButton @click="showingUserRegistration = true">
                        <UserPlusIcon class="w-5 h-5" />
                    </SecondaryButton>
                </Tooltip>
            </div>
        </template>

        <div>
            <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                <div class="text-gray-800 dark:text-gray-200 ">
                    <TableList :list="users" :links="links" :filters="filters" @create="showingUserRegistration = true" />
                </div>
            </div>
        </div>

        <!-- User Registration Modal -->
        <DialogModal :show="showingUserRegistration" @close="showingUserRegistration = false">
            <template #title>
                {{ __('Register New User') }}
            </template>

            <template #content>
                <UserRegistrationForm @close="showingUserRegistration = false" />
            </template>

            <template #footer>
                <SecondaryButton @click="showingUserRegistration = false">
                    {{ __('Cancel') }}
                </SecondaryButton>
            </template>
        </DialogModal>
    </AppLayout>
</template>
