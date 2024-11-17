import { useUserStore } from "@/stores/user";
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
  {
    path: "/register",
    component: () => import("@/pages/Register.vue"),
    name: "register",
  },
  {
    path: "/",
    component: () => import("@/pages/Homepage.vue"),
    name: "homePage",
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

router.beforeEach((to, from) => {
  const exceptRoute = ["login", "register", "homePage"];
  const userStore = useUserStore();
  if (!userStore.isAuthenticated && !exceptRoute.includes(to.name)) {
    return { name: "login", query: { redirect: encodeURIComponent(to.fullPath) } };
  }
});

export default router;
