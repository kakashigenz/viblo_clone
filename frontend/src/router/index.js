import apiClient from "@/api";
import {
  BOOKMARKS_ROUTE_NAME,
  CREATE_ARTICLE_ROUTE_NAME,
  DETAIL_ARTICLE_ROUTE_NAME,
  FOLLOWINGS_ROUTE_NAME,
  HOME_ROUTE_NAME,
  LOGIN_ROUTE_NAME,
  NEWEST_ROUTE_NAME,
  REGISTER_ROUTE_NAME,
} from "@/helper/constant";
import { useUserStore } from "@/stores/user";
import { createRouter, createWebHistory } from "vue-router";

const routes = [
  {
    path: "/publish/article",
    component: () => import("@/pages/EditPost.vue"),
    name: CREATE_ARTICLE_ROUTE_NAME,
  },
  {
    path: "/article/:slug",
    component: () => import("@/pages/DetailArticle.vue"),
    name: DETAIL_ARTICLE_ROUTE_NAME,
  },
  {
    path: "/login",
    component: () => import("@/pages/Login.vue"),
    name: LOGIN_ROUTE_NAME,
  },
  {
    path: "/register",
    component: () => import("@/pages/Register.vue"),
    name: REGISTER_ROUTE_NAME,
  },
  {
    path: "/newest",
    component: () => import("@/pages/Homepage.vue"),
    name: NEWEST_ROUTE_NAME,
  },
  {
    path: "/followings",
    component: () => import("@/pages/Homepage.vue"),
    name: FOLLOWINGS_ROUTE_NAME,
  },
  {
    path: "/bookmarks",
    component: () => import("@/pages/Homepage.vue"),
    name: BOOKMARKS_ROUTE_NAME,
  },
  {
    path: "/",
    redirect: { name: NEWEST_ROUTE_NAME },
    name: HOME_ROUTE_NAME,
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

router.beforeEach(async (to, from) => {
  try {
    const api = apiClient();
    const exceptRoute = [
      LOGIN_ROUTE_NAME,
      REGISTER_ROUTE_NAME,
      NEWEST_ROUTE_NAME,
      FOLLOWINGS_ROUTE_NAME,
      BOOKMARKS_ROUTE_NAME,
      DETAIL_ARTICLE_ROUTE_NAME,
    ];
    const userStore = useUserStore();
    const { data } = await api.auth.isAuthorized();
    userStore.user = data;

    if (!userStore.isAuthenticated && !exceptRoute.includes(to.name)) {
      return {
        name: LOGIN_ROUTE_NAME,
        query: { redirect: encodeURIComponent(to.fullPath) },
      };
    }
  } catch (error) {
    console.log(error);
  }
});

export default router;
