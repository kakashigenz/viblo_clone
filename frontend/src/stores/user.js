import apiClient from "@/api";
import { defineStore } from "pinia";
import { useRouter } from "vue-router";

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
    async logout() {
      try {
        const { data } = await api.auth.logout();
        if (data.message == "success") {
          this.user = null;
        }
      } catch (error) {
        throw error;
      }
    },
  },
});
