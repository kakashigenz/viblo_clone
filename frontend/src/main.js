import { createApp } from "vue";
import App from "./App.vue";
import PrimeVue from "primevue/config";
import Tooltip from "primevue/tooltip";
import Aura from "@primevue/themes/aura";
import ToastService from "primevue/toastservice";
import ConfirmationService from "primevue/confirmationservice";

import "primeicons/primeicons.css";
import "highlight.js/styles/github.css";
import "@/assets/styles/tocbot.css";
import "easymde/dist/easymde.min.css";
import "./style.css";

import { definePreset } from "@primevue/themes";
import router from "./router";
import { createPinia } from "pinia";
import { marked } from "./helper";

const app = createApp(App);

const myPreset = definePreset(Aura, {
  semantic: {
    primary: {
      50: "{blue.50}",
      100: "{blue.100}",
      200: "{blue.200}",
      300: "{blue.300}",
      400: "{blue.400}",
      500: "{blue.500}",
      600: "{blue.600}",
      700: "{blue.700}",
      800: "{blue.800}",
      900: "{blue.900}",
      950: "{blue.950}",
    },
    colorScheme: {
      light: {
        formField: {
          focusBorderColor: "{primary.color}",
        },
      },
    },
  },
});
const pinia = createPinia();
const md = marked();

app.directive("tooltip", Tooltip);
app.use(PrimeVue, {
  theme: {
    preset: myPreset,
    options: {
      prefix: "p",
      darkModeSelector: "system",
      cssLayer: false,
    },
  },
});
app.use(ToastService);
app.use(ConfirmationService);
app.use(pinia);
app.use(router);
app.provide("md", md.getInstance());
app.mount("#app");

//laravel-echo
import Echo from "laravel-echo";

import Pusher from "pusher-js";
window.Pusher = Pusher;

window.Echo = new Echo({
  broadcaster: "reverb",
  key: import.meta.env.VITE_REVERB_APP_KEY,
  wsHost: import.meta.env.VITE_REVERB_HOST,
  wsPort: import.meta.env.VITE_REVERB_PORT,
  wssPort: import.meta.env.VITE_REVERB_PORT,
  forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? "https") === "https",
  enabledTransports: ["ws", "wss"],
});
