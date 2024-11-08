import { createRouter, createWebHistory } from "vue-router";

const routes = [
  {
    path: "/publish/article",
    component: () => import("@/pages/EditPost.vue"),
    name: "createArticle",
  },
  {
    path: "/login",
    component: () => import("@/pages/Login.vue"),
    name: "login",
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

export default router;
