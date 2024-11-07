import axios from "axios";

const apiClient = () => {
  const api = axios.create({
    baseURL: "http://api.viblo.test/api",
    headers: {
      "Content-Type": "application/json",
      Authorization: `Bearer 1|rdtPkpIANhU0EOFd7KgsPykg9NGp2NWVIq2AZTS06394864a`,
    },
  });
  return {
    article: {
      create(article) {
        return api.post("/articles", article);
      },
    },
  };
};

export default apiClient;
