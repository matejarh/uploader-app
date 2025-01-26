import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './vendor/laravel/jetstream/**/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                primary: {
                    '50': '#fff1f2',
                    '100': '#ffe4e6',
                    '200': '#fecdd3',
                    '300': '#fea3ae',
                    '400': '#fc7084',
                    '500': '#f63d5d',
                    '600': '#e31b47',
                    '700': '#d11241',
                    '800': '#a01138',
                    '900': '#891237',
                    '950': '#4d0419',
                 },
                secondary: { '50': '#74b3f2', '100': '#0270dd', '200': '#0265c9', '300': '#0157ad', '400': '#01488e', '500': '#003E7E', '600': '#01366b', '700': '#002a54', '800': '#002244', '900': '#00172d' },
            },

        },
    },

    plugins: [forms, typography],
};
