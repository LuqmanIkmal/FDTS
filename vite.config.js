import { readdirSync } from 'node:fs';
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

// Each file under resources/css/pages and resources/js/pages belongs to the Blade
// view with the same path (fd/index.css -> views/fd/index.blade.php) and is built
// as its own file, so a new page file needs no change here.
const pageFiles = (dir, extension) =>
    readdirSync(dir, { recursive: true })
        .filter((file) => file.endsWith(extension))
        .map((file) => `${dir}/${file.replaceAll('\\', '/')}`);

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                ...pageFiles('resources/css/pages', '.css'),
                ...pageFiles('resources/js/pages', '.js'),
            ],
            refresh: true,
        }),
    ],
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
