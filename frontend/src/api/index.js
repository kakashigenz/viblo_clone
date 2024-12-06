const apiClient = () => {
  return {
    article: {
      create(article) {
        return window.api.post("/articles", article);
      },
      getList(page) {
        return window.api.get("/articles", { params: { page } });
      },
      getObject(slug) {
        return window.api.get(`/articles/${slug}`);
      },
      upvote(id) {
        return window.api.post(`/articles/${id}/upvote`);
      },
      downvote(id) {
        return window.api.post(`/articles/${id}/downvote`);
      },
    },
    auth: {
      getCSRFToken() {
        return window.api.get("sanctum/csrf-cookie", {
          baseURL: "http://api.viblo.test",
        });
      },
      login(credentials) {
        return window.api.post("/spa-login", credentials);
      },
      register(data) {
        return window.api.post("/register", data);
      },
      getUser() {
        return window.api.get("/users/me");
      },
      logout() {
        return window.api.post("/logout");
      },
      isAuthorized() {
        return window.api.post("/check-authorization");
      },
    },
    images: {
      createPresignedURL(data) {
        return window.api.post("/images/create-presigned-url", data);
      },
      getList(page) {
        return window.api.get("images/", { params: { page } });
      },
      uploadImage(file, url) {
        return window.api.put(url, file, {
          headers: {
            "Content-Type": file.type,
          },
        });
      },
      store(data) {
        return window.api.post("/images", data);
      },
    },
    verificationEmail: {
      resendEmail() {
        return window.api.post("/email/verification-notification");
      },
    },
    search: {
      quickSearch(query) {
        return window.api.get("/search/multi", {
          params: {
            q: query,
          },
        });
      },
    },
    comment: {
      getList(slug, page = 1) {
        return window.api.get(`/articles/${slug}/comments`, { params: { page } });
      },
      store(slug, content) {
        return window.api.post(`/articles/${slug}/comments`, { content });
      },
      reply(parrentId, content) {
        return window.api.post(`/comments/${parrentId}/replies`, { content });
      },
      getReplies(parrentId, page = 1) {
        return window.api.get(`/comments/${parrentId}/replies`, {
          params: {
            page,
          },
        });
      },
      update(id, content) {
        return window.api.put(`/comments/${id}`, { content });
      },
      upvote(id) {
        return window.api.post(`/comments/${id}/upvote`);
      },
      downvote(id) {
        return window.api.post(`/comments/${id}/downvote`);
      },
      delete(id) {
        return window.api.delete(`/comments/${id}`);
      },
    },
    vote: {},
  };
};

export default apiClient;
