const defaultTheme = require('tailwindcss/defaultTheme');

/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', 'Nunito', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                primary: {
                    50: '#eff6ff',
                    100: '#dbeafe',
                    200: '#bfdbfe',
                    300: '#93c5fd',
                    400: '#60a5fa',
                    500: '#3b82f6',
                    600: '#2563eb',
                    700: '#1d4ed8',
                    800: '#1e40af',
                    900: '#1e3a8a',
                },
                success: {
                    50: '#f0fdf4',
                    100: '#dcfce7',
                    200: '#bbf7d0',
                    300: '#86efac',
                    400: '#4ade80',
                    500: '#22c55e',
                    600: '#16a34a',
                    700: '#15803d',
                    800: '#166534',
                    900: '#14532d',
                },
                warning: {
                    50: '#fffbeb',
                    100: '#fef3c7',
                    200: '#fde68a',
                    300: '#fcd34d',
                    400: '#fbbf24',
                    500: '#f59e0b',
                    600: '#d97706',
                    700: '#b45309',
                    800: '#92400e',
                    900: '#78350f',
                },
                danger: {
                    50: '#fef2f2',
                    100: '#fee2e2',
                    200: '#fecaca',
                    300: '#fca5a5',
                    400: '#f87171',
                    500: '#ef4444',
                    600: '#dc2626',
                    700: '#b91c1c',
                    800: '#991b1b',
                    900: '#7f1d1d',
                },
            },
            screens: {
                'xs': '475px',
                'sm': '640px',
                'md': '768px',
                'lg': '1024px',
                'xl': '1280px',
                '2xl': '1536px',
                '3xl': '1920px',
                // Custom breakpoints for POS system
                'tablet': '768px',
                'laptop': '1024px',
                'desktop': '1280px',
                'wide': '1536px',
            },
            spacing: {
                '18': '4.5rem',
                '88': '22rem',
                '128': '32rem',
                '144': '36rem',
            },
            borderRadius: {
                'xl': '1rem',
                '2xl': '1.5rem',
                '3xl': '2rem',
            },
            boxShadow: {
                'soft': '0 2px 15px -3px rgba(0, 0, 0, 0.07), 0 10px 20px -2px rgba(0, 0, 0, 0.04)',
                'strong': '0 10px 25px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05)',
                'glow': '0 0 20px rgba(59, 130, 246, 0.15)',
                'glow-lg': '0 0 40px rgba(59, 130, 246, 0.15)',
            },
            animation: {
                'fade-in': 'fadeIn 0.5s ease-in-out',
                'slide-in': 'slideIn 0.3s ease-out',
                'slide-up': 'slideUp 0.3s ease-out',
                'bounce-in': 'bounceIn 0.6s ease-out',
                'pulse-slow': 'pulse 3s infinite',
            },
            keyframes: {
                fadeIn: {
                    '0%': { opacity: '0', transform: 'translateY(10px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                },
                slideIn: {
                    '0%': { transform: 'translateX(-100%)' },
                    '100%': { transform: 'translateX(0)' },
                },
                slideUp: {
                    '0%': { transform: 'translateY(100%)', opacity: '0' },
                    '100%': { transform: 'translateY(0)', opacity: '1' },
                },
                bounceIn: {
                    '0%': { transform: 'scale(0.3)', opacity: '0' },
                    '50%': { transform: 'scale(1.05)' },
                    '70%': { transform: 'scale(0.9)' },
                    '100%': { transform: 'scale(1)', opacity: '1' },
                },
            },
            zIndex: {
                '60': '60',
                '70': '70',
                '80': '80',
                '90': '90',
                '100': '100',
            },
            maxWidth: {
                '8xl': '88rem',
                '9xl': '96rem',
            },
            minHeight: {
                '16': '4rem',
                '20': '5rem',
                '24': '6rem',
                '32': '8rem',
            },
        },
    },

    plugins: [
        require('@tailwindcss/forms'),
        require('@tailwindcss/typography'),
        // Custom plugin for POS-specific utilities
        function({ addUtilities, addComponents, theme }) {
            const newUtilities = {
                '.scrollbar-hide': {
                    /* IE and Edge */
                    '-ms-overflow-style': 'none',
                    /* Firefox */
                    'scrollbar-width': 'none',
                    /* Safari and Chrome */
                    '&::-webkit-scrollbar': {
                        display: 'none'
                    }
                },
                '.scrollbar-thin': {
                    '&::-webkit-scrollbar': {
                        width: '4px',
                        height: '4px',
                    },
                    '&::-webkit-scrollbar-track': {
                        background: '#f1f1f1',
                    },
                    '&::-webkit-scrollbar-thumb': {
                        background: '#c1c1c1',
                        borderRadius: '2px',
                    },
                    '&::-webkit-scrollbar-thumb:hover': {
                        background: '#a8a8a8',
                    },
                },
                '.glass': {
                    'backdrop-filter': 'blur(16px) saturate(180%)',
                    'background-color': 'rgba(255, 255, 255, 0.75)',
                    'border': '1px solid rgba(209, 213, 219, 0.3)',
                },
                '.glass-dark': {
                    'backdrop-filter': 'blur(16px) saturate(180%)',
                    'background-color': 'rgba(17, 24, 39, 0.75)',
                    'border': '1px solid rgba(75, 85, 99, 0.3)',
                },
            }
            
            const newComponents = {
                '.btn': {
                    padding: theme('spacing.2') + ' ' + theme('spacing.4'),
                    borderRadius: theme('borderRadius.lg'),
                    fontWeight: theme('fontWeight.medium'),
                    fontSize: theme('fontSize.sm'),
                    lineHeight: theme('lineHeight.5'),
                    transition: 'all 0.2s ease-in-out',
                    cursor: 'pointer',
                    display: 'inline-flex',
                    alignItems: 'center',
                    justifyContent: 'center',
                    '&:focus': {
                        outline: 'none',
                        boxShadow: '0 0 0 3px rgba(59, 130, 246, 0.1)',
                    },
                    '&:disabled': {
                        opacity: '0.5',
                        cursor: 'not-allowed',
                    },
                },
                '.card-hover': {
                    transition: 'all 0.3s ease-in-out',
                    '&:hover': {
                        transform: 'translateY(-2px)',
                        boxShadow: theme('boxShadow.strong'),
                    },
                },
            }
            
            addUtilities(newUtilities)
            addComponents(newComponents)
        }
    ],
};
