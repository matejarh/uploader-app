/* import { useTranslationsStore } from "./stores/translations";

export const Translator = {
    install: (v) => {
        const store = useTranslationsStore()

        v.config.globalProperties.__ = function(key, replace = {}) {
            let translation = store.translations[key]
                ? store.translations[key]
                : key;

            Object.keys(replace).forEach(function (key) {
                translation = translation.replaceAll(':' + key, replace[key])
            });

            return translation
        };
    },
}; */

export const Translator = {
    install: (v, translations) => {
        v.config.globalProperties.__ = function(key, replace = {}) {
            let translation = translations[key]
                ? translations[key]
                : key;

            Object.keys(replace).forEach(function (key) {
                translation = translation.replaceAll(':' + key, replace[key])
            });

            return translation
        };
    },
};
