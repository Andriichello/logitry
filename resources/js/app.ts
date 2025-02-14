import { createApp, DefineComponent, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3'
import Toast, {POSITION} from "vue-toastification";
import {createPinia} from "pinia";

createInertiaApp({
  resolve: name => {
    const pages = import.meta.glob<DefineComponent>('./Pages/**/*.vue', {eager: true})
    return pages[`./Pages/${name}.vue`]
  },
  setup({el, App, props, plugin}) {
    createApp({render: () => h(App, props)})
      .use(Toast, {
        hideProgressBar: true,
        position: POSITION.BOTTOM_CENTER,
      })
      .use(createPinia())
      .use(plugin)
      .mount(el)
  },
})
