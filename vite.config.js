import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import path from "path";
import { viteStaticCopy } from "vite-plugin-static-copy";

import vue from "@vitejs/plugin-vue";

export default defineConfig({
    server: {
        host: "127.0.0.1",
    },
    plugins: [
        laravel({
            input: ["resources/css/app.css", "resources/js/app.js", 'resources/js/wordpress/homepage-handler.js'],
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
            vue: "vue/dist/vue.esm-bundler.js",
        },
    },
    build: {
        outDir: "public/build",
        manifest: true,
    },
});
