<script setup>
import { ref } from 'vue';

const props = defineProps({
  options: {
    type: Array,
    required: true,
  },
  modelValue: {
    type: String,
    default: '',
  },
});

const emit = defineEmits(['update:modelValue']);

const selectedOption = ref(props.modelValue);

const handleChange = (event) => {
  selectedOption.value = event.target.value;
  emit('update:modelValue', selectedOption.value);
};
</script>

<template>
  <select :value="selectedOption" @change="handleChange" class="border rounded-lg p-2 dark:bg-gray-700 dark:text-white dark:border-gray-600 bg-white text-black border-gray-300">
    <option v-for="option in options" :key="option" :value="option">
      {{ option }}
    </option>
  </select>
</template>
