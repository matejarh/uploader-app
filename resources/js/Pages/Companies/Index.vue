<script setup>
import { computed, onMounted, ref } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { ArrowUpTrayIcon, SquaresPlusIcon } from '@heroicons/vue/24/solid'
import SecondaryButton from '@/Components/SecondaryButton.vue'
import Tooltip from '@/Components/Tooltip.vue'
import DialogModal from '@/Components/DialogModal.vue'
import FileUploader from '@/Components/FileUploader.vue'
import TableList from './Partials/TableList.vue';
import { useForm, usePage } from '@inertiajs/vue3';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';

const props = defineProps({
    companies: Object,
    links: String,
    filters: Object,

})

const page = usePage()

const loadUser = computed(() => {
    return page.props.isAdminOrSuperAdmin
})

const showingCreateDialog = ref(false);

const form = useForm({
    company_name: '',
    company_address: '',
    user_id: null,
});

const store = () => {
    form.post(route('companies.store'), {
        onSuccess: () => {
            showingCreateDialog.value = false;
            form.reset();
        },
    });
};

const users = ref([]);

const fetchUsers = async () => {
    const response = await fetch(route('users.list'));
    users.value = await response.json();
};

onMounted(() => {
    if(loadUser.value) {
        fetchUsers();
    }
    if(page.props.auth.user.companies_count === 0) {
        showingCreateDialog.value = true;
    }
});

</script>

<template>
    <AppLayout :title="__('Companies')">
        <template #header>
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Companies') }}
            </h2>
            <Tooltip :text="__('New Company')">
                <SecondaryButton @click="showingCreateDialog = true">
                    <SquaresPlusIcon class="w-5 h-5" />
                </SecondaryButton>

            </Tooltip>


        </div>
        </template>

        <div>
            <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                <div class="text-gray-800 dark:text-gray-200 ">
                    <TableList :list="companies" :links="links" :filters="filters" :load-user="loadUser" @create="showingCreateDialog = true" />
                </div>
            </div>
        </div>

        <!-- Uploader Modal -->
        <DialogModal :show="showingCreateDialog" @close="showingCreateDialog = false">
            <template #title>
                {{ __('New Company') }}
            </template>

            <template #content>
                <div class="space-y-4">
                    <div class="" v-if="$page.props.isAdminOrSuperAdmin">
                        <InputLabel for="user" class="mb-2" :value="__('Select User')" />

                        <select v-model="form.user_id" id="user" class="border rounded-lg p-2 dark:bg-gray-700 dark:text-white dark:border-gray-600 bg-white text-black border-gray-300 w-full">
                            <option v-for="user in users" :key="user.id" :value="user.id">
                                {{ user.name }}
                            </option>
                        </select>
                    </div>

                    <div>
                        <InputLabel for="company_name" value="Ime podjetja" />
                        <TextInput v-model="form.company_name" class="w-full" id="company_name"
                            placeholder="Vnesite ime podjetja" :hasError="!!form.errors.company_name" required
                            @keyup.enter="store(false)" />
                        <InputError :message="form.errors.company_name" class="mt-2" />
                    </div>

                    <div>
                        <InputLabel for="company_address" value="Naslov podjetja" />
                        <TextInput v-model="form.company_address" class="w-full" id="company_address"
                            placeholder="Vnesite naslov podjetja" :hasError="!!form.errors.company_address" required
                            @keyup.enter="store(false)" />
                        <InputError :message="form.errors.company_address" class="mt-2" />
                    </div>
                </div>
                <!-- <FileUploader @file-uploaded="handleFileUploaded" /> -->
            </template>

            <template #footer>
                <div class="flex justify-end space-x-2">
                    <SecondaryButton :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing" @click="showingCreateDialog = false">
                        {{ __('Cancel') }}
                    </SecondaryButton>
                    <SecondaryButton :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing" @click="store">
                        {{ __('Create') }}
                    </SecondaryButton>
                </div>
            </template>

        </DialogModal>
    </AppLayout>
</template>
