require('./bootstrap');

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/inertia-vue3';
import { InertiaProgress } from '@inertiajs/progress';
import dayjs from 'dayjs';
import relativeTime from 'dayjs/plugin/relativeTime';
import { plugin as Slicksort } from 'vue-slicksort';
import VueSweetalert2 from 'vue-sweetalert2';

dayjs.extend(relativeTime);

const appName = window.document.getElementsByTagName('title')[0]?.innerText || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => require(`./Pages/${name}.vue`),
    setup({ el, app, props, plugin }) {
        return createApp({ render: () => h(app, props) })
            .use(plugin)
            .use(Slicksort)
            .use(VueSweetalert2)
            .mixin({ methods: { route } })
            .mount(el);
    },
});

InertiaProgress.init({ color: '#4B5563' });
