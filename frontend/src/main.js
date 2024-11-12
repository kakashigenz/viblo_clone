import { createApp } from "vue";
import App from "./App.vue";
import PrimeVue from "primevue/config";
import Tooltip from "primevue/tooltip";
import Aura from "@primevue/themes/aura";
import ToastService from "primevue/toastservice";

import "primeicons/primeicons.css";
import "easymde/dist/easymde.min.css";
import "./style.css";

import { definePreset } from "@primevue/themes";
import router from "./router";

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
app.use(router);
app.mount("#app");
