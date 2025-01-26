import { defineStore } from 'pinia'
import { ref } from 'vue'

export const useTranslationsStore = defineStore('translations', () => {
    const translations = ref([])


    const updateTranslations = (newTranslations) => {
        translations.value = newTranslations
    }

    return { translations, updateTranslations }
})
