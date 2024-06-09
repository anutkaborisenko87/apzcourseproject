/** @type {import('tailwindcss').Config} */
export default {
    content: ['./src/**/*.{js,jsx,ts,tsx}', './public/index.html'],
    darkMode: 'media', // or 'media' or 'class'
    theme: {
        extend: {
            colors: {
                beige: '#d3d9e1',
            },
        },
    },
    plugins: [],
    animation: true,
}

