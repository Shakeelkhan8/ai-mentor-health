import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                // Brand emerald, used for the logo, accents, focus rings and links.
                brand: {
                    50: '#ecfdf7',
                    100: '#d1fae9',
                    200: '#a7f3d4',
                    300: '#6ee7bb',
                    400: '#34d39e',
                    500: '#10b985',
                    600: '#059669',
                    700: '#047857',
                    800: '#065f46',
                    900: '#064e3b',
                    950: '#022c22',
                },
                // Near-black with a green bias for dark marketing sections.
                ink: {
                    DEFAULT: '#0b1412',
                    soft: '#111c19',
                    line: '#1e2b27',
                },
            },
        },
    },

    plugins: [forms],
};
