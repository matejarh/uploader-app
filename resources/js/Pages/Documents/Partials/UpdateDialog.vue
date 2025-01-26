<script setup>
import { useForm } from '@inertiajs/vue3';
import DialogModal from '@/Components/DialogModal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import { CheckIcon } from '@heroicons/vue/24/solid';

import ActionMessage from '@/Components/ActionMessage.vue';
import { watchEffect } from 'vue';

const props = defineProps({
    document: Object,
    show: Boolean,

})
const form = useForm({
    leto: '',
    delovnih_dni: 0,
    delovnih_ur: 0,
    praznikov_dni: 0,
    praznikov_ur: 0,
    back: false,
})

const emit = defineEmits(['close'])

const store = (close = false) => {
    form.put(route('admin.leto.update', props.leto), {
        preserveScroll: true,
        errorBag: 'updatingLeto',
        onSuccess: () => {
            if (close) emit('close')
            // form.reset()
        }
    })
}

watchEffect(async () => {
    form.leto = props.leto.leto || ''
    form.delovnih_dni = props.leto.delovnih_dni || ''
    form.delovnih_ur = props.leto.delovnih_ur || ''
    form.praznikov_dni = props.leto.praznikov_dni || ''
    form.praznikov_ur = props.leto.praznikov_ur || ''

});
</script>

<template>
    <DialogModal :show="show" @close="$emit('close')">
        <template #title>
            Urejanje leta {{ form.leto }}
        </template>

        <template #content>
            <div class="space-y-4">

                <div class="">
                    <InputLabel for="leto" value="Leto" />
                    <TextInput v-model="form.leto" class="w-full" id="leto" placeholder="Vnesite naziv leta npr. 2024"
                        :hasError="!!form.errors.leto" required @keyup.enter="store(false)" disabled readonly/>
                    <InputError :message="form.errors.leto" class="mt-2" />
                </div>

                <div class="md:flex md:space-x-2">

                    <div class="w-full">
                        <InputLabel for="delovnih_dni" value="Število delovnih dni" />
                        <TextInput v-model="form.delovnih_dni" class="w-full" id="delovnih_dni"
                            placeholder="Vnesite število delovnih dni" :hasError="!!form.errors.delovnih_dni" required
                            @keyup.enter="store(false)" @input="form.delovnih_ur = form.delovnih_dni * 8" />
                        <InputError :message="form.errors.delovnih_dni" class="mt-2" />

                    </div>

                    <div class="w-full">
                        <InputLabel for="delovnih_ur" value="Število delovnih ur" />
                        <TextInput v-model="form.delovnih_ur" class="w-full" id="delovnih_ur"
                            placeholder="Vnesite število delovnih ur" :hasError="!!form.errors.delovnih_ur" required
                            @keyup.enter="store(false)" @input="form.delovnih_dni = form.delovnih_ur / 8" />
                        <InputError :message="form.errors.delovnih_ur" class="mt-2" />

                    </div>
                </div>

                <div class="md:flex md:space-x-2">
                    <div class="w-full">
                        <InputLabel for="praznikov_dni" value="Število praznikov dni" />
                        <TextInput v-model="form.praznikov_dni" class="w-full" id="praznikov_dni"
                            placeholder="Vnesite število prazničnih dni" :hasError="!!form.errors.praznikov_dni"
                            required @keyup.enter="store(false)" @input="form.praznikov_ur = form.praznikov_dni * 8" />
                        <InputError :message="form.errors.praznikov_dni" class="mt-2" />

                    </div>
                    <div class="w-full">
                        <InputLabel for="praznikov_ur" value="Število praznikov ur" />
                        <TextInput v-model="form.praznikov_ur" class="w-full" id="praznikov_ur"
                            placeholder="Vnesite število prazničnih ur" :hasError="!!form.errors.praznikov_ur" required
                            @keyup.enter="store(false)" @input="form.praznikov_dni = form.praznikov_ur / 8" />
                        <InputError :message="form.errors.praznikov_ur" class="mt-2" />
                    </div>
                </div>
            </div>
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
