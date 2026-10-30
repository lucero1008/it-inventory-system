import { createApp, h } from 'vue'
import { createInertiaApp, router } from '@inertiajs/vue3'
import { initFlowbite } from 'flowbite';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';

createInertiaApp({
    resolve: name => {
        const pages = import.meta.glob('./Pages/**/*.vue', { eager: true })
        return pages[`./Pages/${name}.vue`]
    },
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .mount(el)
    },
    progress: {
        color: '#2b7fff',
    },
});

router.on('navigate', () => {
    setTimeout(() => {
        initFlowbite();
    }, 0);
});
