import apiClient from "@/api";
import { defineStore } from "pinia";

const api = apiClient();

export const useUserStore = defineStore("user", {
  state: () => ({
    user: null,
  }),
  getters: {
    isAuthenticated() {
      return !!this.user;
    },
  },
  actions: {
    async login(value) {
      try {
        const { status } = await api.auth.getCSRFToken();
        if (status == 204) {
          const { data } = await api.auth.login(value);
          if (data.message == "success") {
            this.user = data.user;
          }
        }
      } catch (error) {
        throw error;
      }
    },
  },
});
