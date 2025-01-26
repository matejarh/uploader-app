<script setup>
import { ref } from 'vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Checkbox from '@/Components/Checkbox.vue';

const password = ref('');
const length = ref('24');
const includeUppercase = ref(true);
const includeLowercase = ref(true);
const includeNumbers = ref(true);
const includeSymbols = ref(true);

const emit = defineEmits(['generated']);

const generatePassword = () => {
    const uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    const lowercase = 'abcdefghijklmnopqrstuvwxyz';
    const numbers = '0123456789';
    const symbols = '!@#$%^&*()_+~`|}{[]:;?><,./-=';

    let characters = '';
    if (includeUppercase.value) characters += uppercase;
    if (includeLowercase.value) characters += lowercase;
    if (includeNumbers.value) characters += numbers;
    if (includeSymbols.value) characters += symbols;

    let generatedPassword = '';
    for (let i = 0; i < length.value; i++) {
        generatedPassword += characters.charAt(Math.floor(Math.random() * characters.length));
    }

    password.value = generatedPassword;
    emit('generated', generatedPassword);
};
</script>

<template>
    <div class="p-6 bg-white dark:bg-gray-800 rounded-lg">
        <div class="mb-4">
            <InputLabel for="length" :value="__('Length')" />
            <TextInput
                    id="length"
                    v-model="length"
                    type="number"
                    class="mt-1 block w-full"
                    required
                    autofocus
                    autocomplete="name"
                    min="12" max="128"
                />

        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="mb-0">
                <InputLabel for="upper">
                    <div class="flex items-center">
                        <Checkbox id="upper" v-model:checked="includeUppercase" name="upper" required />

                        <div class="ms-2">
                            {{ __('Include Uppercase Letters') }}
                        </div>
                    </div>
                </InputLabel>
            </div>

            <div class="mb-0">
                <InputLabel for="lower">
                    <div class="flex items-center">
                        <Checkbox id="lower" v-model:checked="includeLowercase" name="lower" required />

                        <div class="ms-2">
                            {{ __('Include Lowercase Letters') }}
                        </div>
                    </div>
                </InputLabel>
            </div>
            <div class="mb-4">
                <InputLabel for="numbers">
                    <div class="flex items-center">
                        <Checkbox id="numbers" v-model:checked="includeNumbers" name="numbers" required />

                        <div class="ms-2">
                            {{ __('Include Numbers') }}
                        </div>
                    </div>
                </InputLabel>
            </div>
            <div class="mb-4">
                <InputLabel for="symbols">
                    <div class="flex items-center">
                        <Checkbox id="symbols" v-model:checked="includeSymbols" name="symbols" required />

                        <div class="ms-2">
                            {{ __('Include Symbols') }}
                        </div>
                    </div>
                </InputLabel>
            </div>
        </div>

        <button @click="generatePassword" class="w-full px-4 py-2 bg-blue-600 text-white font-semibold rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:focus:ring-offset-gray-800">
            {{ __('Generate Password') }}
        </button>
        <div v-if="password" class="mt-4">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Generated Password') }}</label>
            <input type="text" :value="password" readonly class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm dark:bg-gray-700 dark:text-gray-300" />
        </div>
    </div>
</template>
