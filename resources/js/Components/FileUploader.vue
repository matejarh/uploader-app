<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { Cog6ToothIcon } from '@heroicons/vue/24/outline';

const selectedFile = ref(null);

const emit = defineEmits(['fileUploaded']);

const form = useForm({
  file: null,
});

const handleFileUpload = (event) => {
    const file = event.target.files[0];
    if (!file) return;

    const allowedTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
    const maxSize = 2 * 1024 * 1024; // 2MB

    if (!allowedTypes.includes(file.type)) {
        alert('Invalid file type. Only PDF, DOC, and DOCX are allowed.');
        return;
    }

    if (file.size > maxSize) {
        alert('File is too large. Maximum size is 2MB.');
        return;
    }

    //selectedFile.value = event.target.files[0];
    selectedFile.value = file;
    form.file = selectedFile.value;
};

const submitFile = () => {
  if (!selectedFile.value) {
    alert("Prosimo, izberite datoteko za nalaganje.");
    return;
  }

  form.post('/upload', {
    onStart: () => {
      form.clearErrors();
      form.progress = 0;
    },
    onProgress: (event) => {
      form.progress = Math.round((event.loaded / event.total) * 100);
    },
    onSuccess: () => {
      form.reset();
      selectedFile.value = null;
      emit('fileUploaded', true);
      // alert("File uploaded successfully!");
    },
    onError: () => {
        emit('fileUploaded', false);
      // alert("File upload failed!");
    },
  });
};

const handleDrop = (event) => {
  event.preventDefault();
  const files = event.dataTransfer.files;
  if (files.length > 0) {
    selectedFile.value = files[0];
    form.file = selectedFile.value;
  }
};

const handleDragOver = (event) => {
  event.preventDefault();
};
</script>

<template>
  <div class="p-6">
    <!-- <h2 class="text-xl font-bold mb-4">{{ __('Upload Your File') }}</h2> -->


    <div class="flex items-center justify-center w-full"
        @drop="handleDrop"
        @dragover="handleDragOver">
        <label for="dropzone-file" class="flex flex-col items-center justify-center w-full h-64 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 dark:hover:bg-gray-800 dark:bg-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:hover:border-gray-500 dark:hover:bg-gray-600">
        <div class="flex flex-col items-center justify-center pt-5 pb-6">
          <svg class="w-8 h-8 mb-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2"/>
          </svg>
          <span v-if="!selectedFile">
            <p class="mb-2 text-sm text-gray-500 dark:text-gray-400"><span class="font-semibold">{{ __('Click to upload') }}</span> {{ __('or drag and drop') }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('PDF, DOC, DOCX (MAX. 2MB)') }}</p>
          </span>
          <span v-else>
            <p class="mb-2 text-sm text-gray-500 dark:text-gray-400"><span class="font-semibold">{{ selectedFile.name }}</span></p>
            <p class="text-xs text-gray-500 dark:text-gray-400">{{ selectedFile.size }} {{ __('bytes') }}</p>
          </span>
        </div>
        <input id="dropzone-file" type="file" class="hidden" @change="handleFileUpload" />
      </label>
    </div>

   <!--  <input type="file" @change="handleFileUpload" class="mb-4" /> -->
    <button @click="submitFile" :disabled="form.processing" class="px-4 py-2 bg-blue-600 text-white rounded-md w-full">
        <Cog6ToothIcon class="w-4 h-4 inline-block animate-spin" v-if="form.processing" />
      {{ form.processing ? __('Uploading...') : __('Upload') }}
    </button>
    <div v-if="form.progress" class="mt-4">
      <progress :value="form.progress" max="100" class="w-full"></progress>
      <span>{{ form.progress }}%</span>
    </div>
    <div v-if="form.errors.file" class="mt-4 text-red-600">
      {{ form.errors.file }}
    </div>
  </div>
</template>
