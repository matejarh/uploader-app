<script setup>
import { router, useForm } from '@inertiajs/vue3';
import DialogModal from '@/Components/DialogModal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import { CheckIcon } from '@heroicons/vue/24/solid';

import ActionMessage from '@/Components/ActionMessage.vue';
import { ref, watchEffect } from 'vue';

const props = defineProps({
    company: Object,
    show: Boolean,
});

const logoPreview = ref(null);
const logoInput = ref(null);

const form = useForm({
    _method: 'PUT',
    company_name: '',
    company_address: '',
    photo: null,
});

const emit = defineEmits(['close']);

const store = (close = false) => {
    // Attach the selected logo file to the form
    if (logoInput.value && logoInput.value.files.length > 0) {
        form.photo = logoInput.value.files[0];
    }

    form.post(route('companies.update', props.company), {
        preserveScroll: true,
        onSuccess: () => {
            emit('close');
            clearLogoFileInput();
        },
    });
};

const selectNewLogo = () => {
    logoInput.value.click();
};

const updateLogoPreview = () => {
    const logo = logoInput.value.files[0];

    if (!logo) return;

    const reader = new FileReader();

    reader.onload = (e) => {
        logoPreview.value = e.target.result;
    };

    reader.readAsDataURL(logo);
};

const deleteLogo = () => {
    router.delete(route('companies.destroyLogo', props.company), {
        preserveScroll: true,
        onSuccess: () => {
            logoPreview.value = null;
            clearLogoFileInput();
        },
    });
};

const clearLogoFileInput = () => {
    if (logoInput.value?.value) {
        logoInput.value.value = null;
    }
};

watchEffect(() => {
    form.company_name = props.company ? props.company.company_name : '';
    form.company_address = props.company ? props.company.company_address : '';
});
</script>

<template>
    <DialogModal :show="show" @close="$emit('close')">
        <template #title>
            Urejanje podjetja {{ props.company.company_name }}
        </template>

        <template #content>
            <form @submit.prevent="store(false)">
                <!-- Logo Photo -->
                <div class="col-span-6 sm:col-span-4 mb-4">
                    <!-- Logo Photo File Input -->
                    <input id="photo" ref="logoInput" type="file" accept=".jpg,.jpeg,.png,.svg" class="hidden"
                        @change="updateLogoPreview" />

                    <InputLabel for="photo" :value="__('Logo')" />

                    <!-- Current Logo Photo -->
                    <div v-show="!logoPreview" class="mt-2">
                        <img :src="company.logo_photo_url" :alt="company.company_slug"
                            class="rounded-full size-20 object-cover" />
                    </div>

                    <!-- New Logo Photo Preview -->
                    <div v-show="logoPreview" class="mt-2">
                        <span class="block rounded-full size-20 bg-cover bg-no-repeat bg-center"
                            :style="'background-image: url(\'' + logoPreview + '\');'" />
                    </div>

                    <SecondaryButton class="mt-2 me-2" type="button" @click.prevent="selectNewLogo">
                        {{ __('Select New Logo') }}
                    </SecondaryButton>

                    <SecondaryButton v-if="company.logo_photo_path" type="button" class="mt-2"
                        @click.prevent="deleteLogo">
                        {{ __('Remove Logo') }}
                    </SecondaryButton>

                    <InputError :message="form.errors.photo" class="mt-2" />
                </div>

                <div class="space-y-4">
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
            </form>
        </template>

        <template #footer>
            <ActionMessage :on="form.recentlySuccessful">
                <CheckIcon class="w-6 h-6 text-green-500" />
            </ActionMessage>

            <PrimaryButton type="button" class="ms-3" :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing" @click="store(true)">
                {{ form.processing ? 'shranjujem...' : 'Shrani' }}
            </PrimaryButton>

            <SecondaryButton @click="$emit('close')">
                Prekliči
            </SecondaryButton>
        </template>
    </DialogModal>
</template>
