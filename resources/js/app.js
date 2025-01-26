import './bootstrap';
import '../css/app.css';

import { createPinia } from 'pinia'
import { createApp, h } from 'vue';
import { createInertiaApp, Link } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import { Translator } from './Translator.js';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';
const pinia = createPinia()

/* updateGlobalOptions({
    autoClose: 3000,
    style: {
        opacity: '1',
        userSelect: 'initial',
    },
    position: toast.POSITION.TOP_RIGHT,
    theme: window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches ? "dark" : "light"
    // theme: "dark"
}); */

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .use(pinia)
            .component('InertiaLink', Link)
            .use(Translator, props.initialPage.props.translations)
            .mount(el);
    },
    progress: {
        color: '#00B8D4',
    },
});
