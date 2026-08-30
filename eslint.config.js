import js from '@eslint/js';
import tsPlugin from 'typescript-eslint';

export default [
    js.configs.recommended,
    ...tsPlugin.configs.recommended,
    {
        files: ['resources/js/**/*.{ts,tsx}'],
        rules: {
            '@typescript-eslint/no-explicit-any': 'error',
            '@typescript-eslint/no-unused-vars': ['warn', { argsIgnorePattern: '^_' }],
        },
    },
    {
        ignores: ['vendor/**', 'public/**', 'node_modules/**', 'dist/**', 'storage/**'],
    },
];
