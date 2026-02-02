// ABOUTME: ESLint flat config for HeirLuxury JS files.
// ABOUTME: Uses recommended rules with Prettier integration for Alpine.js/Vite stack.
import js from "@eslint/js";
import prettier from "eslint-config-prettier";

export default [
    js.configs.recommended,
    prettier,
    {
        files: ["resources/js/**/*.js"],
        languageOptions: {
            ecmaVersion: 2022,
            sourceType: "module",
            globals: {
                Alpine: "readonly",
                axios: "readonly",
                window: "readonly",
                document: "readonly",
                console: "readonly",
                fetch: "readonly",
            },
        },
    },
    {
        ignores: ["vendor/**", "node_modules/**", "public/build/**", "storage/**"],
    },
];
