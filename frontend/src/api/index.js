import axios from "axios";

const apiClient = () => {
  axios.defaults.withCredentials = true;
  axios.defaults.withXSRFToken = true;
  const api = axios.create({
    baseURL: "http://api.viblo.test/api",
    headers: {
      "Content-Type": "application/json",
    },
  });
  return {
    article: {
      create(article) {
        return api.post("/articles", article);
      },
    },
    auth: {
      getCSRFToken() {
        return api.get("sanctum/csrf-cookie", {
          baseURL: "http://api.viblo.test",
        });
      },
      login(credentials) {
        return api.post("spa-login", credentials);
      },
      register(data) {
        return api.post("/register", data);
      },
      getUser() {
        return api.get("users/me");
      },
    },
    verificationEmail: {
      resendEmail() {
        return api.post("/email/verification-notification");
      },
    },
  };
};

export default apiClient;
