import { defineConfig, loadEnv } from "vite";
import laravel from "laravel-vite-plugin";
import vue from "@vitejs/plugin-vue";
import path from "path";

export default defineConfig(({ mode }) => {
    const env = loadEnv(mode, process.cwd(), "VITE_");

    return {
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
            host: env.VITE_SERVER_HOST,
            port: parseInt(env.VITE_SERVER_PORT, 10),
            strictPort: true,
            hmr: {
                host: env.VITE_HMR_HOST,
                port: parseInt(env.VITE_HMR_PORT, 10),
            },
            cors: true,
        },

        define: {
            __APP_NAME__: JSON.stringify(env.VITE_APP_NAME),
            __VITE_SERVER_HOST__: JSON.stringify(env.VITE_SERVER_HOST),
            __VITE_SERVER_PORT__: parseInt(env.VITE_SERVER_PORT, 10),
            __VITE_HMR_HOST__: JSON.stringify(env.VITE_HMR_HOST),
            __VITE_HMR_PORT__: parseInt(env.VITE_HMR_PORT, 10),
        },
    };
});
