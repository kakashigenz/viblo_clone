import axios from "axios";

const apiClient = (customHeader) => {
  axios.defaults.withCredentials = true;
  axios.defaults.withXSRFToken = true;
  const api = axios.create({
    baseURL: "http://api.viblo.test/api",
    headers: {
      "Content-Type": "application/json",
      ...customHeader,
    },
  });
  return {
    article: {
      create(article) {
        return api.post("/articles", article);
      },
      getList(page) {
        return api.get("/articles", { params: { page } });
      },
      getObject(slug) {
        return api.get(`/articles/${slug}`);
      },
      upvote(id) {
        return api.post(`/articles/${id}/upvote`);
      },
      downvote(id) {
        return api.post(`/articles/${id}/downvote`);
      },
    },
    auth: {
      getCSRFToken() {
        return api.get("sanctum/csrf-cookie", {
          baseURL: "http://api.viblo.test",
        });
      },
      login(credentials) {
        return api.post("/spa-login", credentials);
      },
      register(data) {
        return api.post("/register", data);
      },
      getUser() {
        return api.get("/users/me");
      },
      logout() {
        return api.post("/logout");
      },
      isAuthorized() {
        return api.post("/check-authorization");
      },
    },
    images: {
      createPresignedURL(data) {
        return api.post("/images/create-presigned-url", data);
      },
      getList(page) {
        return api.get("images/", { params: { page } });
      },
      uploadImage(file, url) {
        return api.put(url, file, {
          headers: {
            "Content-Type": file.type,
          },
        });
      },
      store(data) {
        return api.post("/images", data);
      },
    },
    verificationEmail: {
      resendEmail() {
        return api.post("/email/verification-notification");
      },
    },
    search: {
      quickSearch(query) {
        return api.get("/search/multi", {
          params: {
            q: query,
          },
        });
      },
    },
    comment: {
      getList(slug, page = 1) {
        return api.get(`/articles/${slug}/comments`, { params: { page } });
      },
      store(slug, content) {
        return api.post(`/articles/${slug}/comments`, { content });
      },
      reply(parrentId, content) {
        return api.post(`/comments/${parrentId}/replies`, { content });
      },
      getReplies(parrentId, page = 1) {
        return api.get(`/comments/${parrentId}/replies`, {
          params: {
            page,
          },
        });
      },
      update(id, content) {
        return api.put(`/comments/${id}`, { content });
      },
      upvote(id) {
        return api.post(`/comments/${id}/upvote`);
      },
      downvote(id) {
        return api.post(`/comments/${id}/downvote`);
      },
      delete(id) {
        return api.delete(`/comments/${id}`);
      },
    },
    vote: {},
  };
};

export default apiClient;
