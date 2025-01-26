<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import PasswordGenerator from '@/Components/PasswordGenerator.vue';
import DialogModal from '@/Components/DialogModal.vue';

const displayingPasswordGenerator = ref(false);

const emit = defineEmits(['close']);

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('users.store'), {
        onSuccess: () => {
            form.reset();
            emit('close');
        },
    });
};

const handleGeneratedPassword = (password) => {
    form.password = password;
    form.password_confirmation = password;

    displayingPasswordGenerator.value = false;
};

</script>

<template>
    <form @submit.prevent="submit">
        <div>
            <InputLabel for="name" :value="__('Name')" />
            <TextInput
                id="name"
                v-model="form.name"
                type="text"
                class="mt-1 block w-full"
                required
                autofocus
                autocomplete="name"
            />
            <InputError class="mt-2" :message="form.errors.name" />
        </div>

        <div class="mt-4">
            <InputLabel for="email" :value="__('Email')" />
            <TextInput
                id="email"
                v-model="form.email"
                type="email"
                class="mt-1 block w-full"
                required
                autocomplete="username"
            />
            <InputError class="mt-2" :message="form.errors.email" />
        </div>

        <div class="mt-4">
            <InputLabel for="password" :value="__('Password')" />
            <TextInput
                id="password"
                v-model="form.password"
                type="password"
                class="mt-1 block w-full"
                required
                autocomplete="new-password"
            />
            <InputError class="mt-2" :message="form.errors.password" />
        </div>

        <div class="mt-4">
            <InputLabel for="password_confirmation" :value="__('Confirm Password')" />
            <TextInput
                id="password_confirmation"
                v-model="form.password_confirmation"
                type="password"
                class="mt-1 block w-full"
                required
                autocomplete="new-password"
            />
            <InputError class="mt-2" :message="form.errors.password_confirmation" />
        </div>

        <div class="mt-4 flex justify-end">
            <SecondaryButton @click="displayingPasswordGenerator = true" class="me-4">
                {{ __('Generate Password') }}
            </SecondaryButton>

            <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                {{ __('Register') }}
            </PrimaryButton>
        </div>
    </form>

    <!-- Password Generator Modal -->
    <DialogModal :show="displayingPasswordGenerator" @close="displayingPasswordGenerator = false">
        <template #title>
            {{ __('Password Generator') }}
        </template>

        <template #content>
            <PasswordGenerator @generated="handleGeneratedPassword" />
        </template>

        <template #footer>
            <SecondaryButton @click="displayingPasswordGenerator = false">
                {{ __('Close') }}
            </SecondaryButton>
        </template>
    </DialogModal>
</template>
