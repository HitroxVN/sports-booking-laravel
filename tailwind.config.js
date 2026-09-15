import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    darkMode: 'class',

    theme: {
        extend: {
            colors: {
                // ── HỆ MÀU NHÀ THUỐC LONG CHÂU (markdown/DESIGN.md) ──
                // brand = --brand-color-1 #0037C1 (xanh chủ đạo)
                brand: {
                    50: '#EAEFFA',
                    100: '#D5E2F7',
                    200: '#B3C7EF',
                    300: '#85A5E4',
                    400: '#587FD6',
                    500: '#2F5DC8',
                    600: '#0037C1',
                    700: '#002E9F',
                    800: '#00257E',
                    900: '#001B5C',
                    950: '#001238',
                },
                // cta = vàng Long Châu — nút hành động chính, chữ đen
                cta: {
                    50: '#FFF8DB',
                    100: '#FFEEB0',
                    200: '#FFE382',
                    300: '#FFD54D',
                    400: '#FFCD00',
                    500: '#FFCD00',
                    600: '#F5B800',
                    700: '#DB9E00',
                },
                // tint = các nền pastel trích xuất từ palette Long Châu
                tint: {
                    blue: '#EAEFFA',
                    sky: '#DFECF4',
                    aqua: '#D4F1F4',
                    mint: '#E6EED6',
                    lavender: '#DCC8FF',
                    violet: '#EDE9FE',
                    pink: '#FAD2E1',
                    peach: '#FFDAB9',
                    cream: '#F1EEE4',
                    beige: '#F7EDE2',
                },
                // accent = màu nhấn phụ từ palette
                accent: {
                    teal: '#116466',
                    olive: '#A7C957',
                    moss: '#6BA368',
                    brown: '#654321',
                    violet: '#5D3B99',
                    purple: '#4A01E7',
                    lilac: '#967BB6',
                    slate: '#4C5C68',
                },
                // Alias: giữ tên "primary" trỏ về scale brand xanh
                // để toàn bộ class sẵn có (primary-600, primary-50...)
                // tự động chuyển sang hệ màu Long Châu.
                primary: {
                    50: '#EAEFFA',
                    100: '#D5E2F7',
                    200: '#B3C7EF',
                    300: '#85A5E4',
                    400: '#587FD6',
                    500: '#2F5DC8',
                    600: '#0037C1',
                    700: '#002E9F',
                    800: '#00257E',
                    900: '#001B5C',
                    950: '#001238',
                },
            },
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
            },
            boxShadow: {
                // Shadow mềm tông xanh theo elevation của Long Châu
                lc: '0 2px 6px -2px rgba(0, 17, 45, 0.03), 0 2px 16px -4px rgba(0, 17, 45, 0.06)',
                'lc-lg': '0 0 16px -4px rgba(0, 39, 102, 0.08), 0 0 6px -2px rgba(0, 39, 102, 0.03)',
            },
        },
    },

    plugins: [forms],
};
