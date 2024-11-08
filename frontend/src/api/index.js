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
    },
  };
};

export default apiClient;
