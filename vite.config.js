import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import vue from "@vitejs/plugin-vue";
import path from "path";

export default defineConfig({
    plugins: [
        laravel({
            input: "resources/js/app.js",
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],

    resolve: {
        alias: {
            "@": path.resolve(__dirname, "resources/js"),
        },
    },

    css: {
        preprocessorOptions: {
            scss: {
                additionalData: `@use "vuetify/styles" as *;`,
            },
        },
    },

    server: {
        host: true,
        port: 8098,
        strictPort: true,
        hmr: {
            host: "192.168.0.146",
            port: 8098,
        },
        cors: true,
    },
});