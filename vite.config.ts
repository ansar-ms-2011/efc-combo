import path from 'path';
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin'
import vue from '@vitejs/plugin-vue';
import VueI18nPlugin from '@intlify/unplugin-vue-i18n/vite';
import {VitePWA} from 'vite-plugin-pwa';

export default defineConfig({
    base: '/build/',
    plugins: [
        laravel({
            input: ['resources/js/main.ts', 'resources/css/app.css'],
            refresh: true,
        }),
        vue({
            template: {
                compilerOptions: {
                    isCustomElement: (tag) => tag.startsWith('cropper-')
                }
            }
        }),
        VueI18nPlugin({
            include: path.resolve(__dirname, './resources/js/locales/**'),
        }),
        VitePWA({
            filename: 'sw.js',
            registerType: 'autoUpdate',
            injectRegister: 'auto',
            workbox: {
                maximumFileSizeToCacheInBytes: 50 * 1024 * 1024, // 10 MiB (add this line)
                runtimeCaching: [
                    {
                        urlPattern: ({url}) =>
                            url.pathname.startsWith('/api/'),

                        handler: 'NetworkOnly',
                        options: {
                            backgroundSync: {
                                name: 'applicationQueue',
                                options: {
                                    maxRetentionTime: 24 * 60, // 24 hours (minutes)
                                },
                            },
                        },
                    },
                    {
                        urlPattern: /\.(?:png|jpg|jpeg|svg|gif)$/i,
                        handler: 'CacheFirst',
                        options: {
                            cacheName: 'images-cache',
                            expiration: {
                                maxEntries: 50,
                                maxAgeSeconds: 30 * 24 * 60 * 60,
                            },
                        },
                    },
                ],
            },
            includeAssets: ['favicon.svg', 'robots.txt', 'apple-touch-icon.png'],
            manifest: {
                name: 'E-Facilitation Centre AJK',
                short_name: 'EF-Facilitation Centre AJK',
                description: 'This is a PWA version of E-Facilitation Center AJK App',
                theme_color: '#ffffff',
                background_color: '#ffffff',
                display: 'standalone',
                start_url: '/',
                icons: [
                    {
                        src: '/pwa/pwa-192x192.png',
                        sizes: '192x192',
                        type: 'image/png'
                    },
                    {
                        src: '/pwa/pwa-512x512.png',
                        sizes: '512x512',
                        type: 'image/png'
                    },
                    {
                        src: '/pwa/pwa-512x512.png',
                        sizes: '512x512',
                        type: 'image/png',
                        purpose: 'any maskable'
                    }
                ]
            }
        })
    ],
    resolve: {
        alias: {
            '@': path.resolve(__dirname, './resources/js'),
            '@assets': path.resolve(__dirname, './resources/js/assets'),
            '@css': path.resolve(__dirname, './resources/css'),
        },
    },
    build: {
        rollupOptions: {
            external: (id) => {
                return id === 'onnxruntime-web/webgpu' || id.includes('onnxruntime-web');
            },
            output: {
                // Your output config
            }
        }
    },
    optimizeDeps: {
        include: ['quill'],
    },
});
