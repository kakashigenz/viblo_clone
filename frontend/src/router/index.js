import apiClient from "@/api";
import {
  ARTICLE_MANAGEMENT_ROUTE_NAME,
  BOOKMARKS_ROUTE_NAME,
  CREATE_ARTICLE_ROUTE_NAME,
  DETAIL_ARTICLE_ROUTE_NAME,
  EDIT_ARTICLE_ROUTE_NAME,
  FOLLOWINGS_ROUTE_NAME,
  FORGOT_PASSWORD_ROUTE_NAME,
  HOME_ROUTE_NAME,
  IMAGE_MANAGEMENT_ROUTE_NAME,
  INFO_MANAGEMENT_ROUTE_NAME,
  LOGIN_ROUTE_NAME,
  NEWEST_ROUTE_NAME,
  NOT_FOUND_ROUTE_NAME,
  PASSWORD_MANAGEMENT_ROUTE_NAME,
  REGISTER_ROUTE_NAME,
  RESET_PASSWORD_ROUTE_NAME,
} from "@/helper/constant";
import { useUserStore } from "@/stores/user";
import { createRouter, createWebHistory } from "vue-router";

const routes = [
  {
    path: "/publish/article",
    component: () => import("@/pages/EditArticle.vue"),
    name: CREATE_ARTICLE_ROUTE_NAME,
  },
  {
    path: "/articles/:slug",
    component: () => import("@/pages/DetailArticle.vue"),
    name: DETAIL_ARTICLE_ROUTE_NAME,
  },
  {
    path: "/articles/:slug/edit",
    component: () => import("@/pages/EditArticle.vue"),
    name: EDIT_ARTICLE_ROUTE_NAME,
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
    path: "/management",
    component: () => import("@/pages/Management.vue"),
    children: [
      {
        path: "articles/:status",
        component: () => import("@/pages/ArticleList.vue"),
        name: ARTICLE_MANAGEMENT_ROUTE_NAME,
      },
      {
        path: "images",
        component: () => import("@/pages/ImageList.vue"),
        name: IMAGE_MANAGEMENT_ROUTE_NAME,
      },
      {
        path: "info",
        component: () => import("@/pages/AccountInfo.vue"),
        name: INFO_MANAGEMENT_ROUTE_NAME,
      },
      {
        path: "password",
        component: () => import("@/pages/ChangePassword.vue"),
        name: PASSWORD_MANAGEMENT_ROUTE_NAME,
      },
    ],
    redirect: { name: ARTICLE_MANAGEMENT_ROUTE_NAME, params: { status: "drafts" } },
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
    path: "/forgot-password",
    component: () => import("@/pages/ForgotPassword.vue"),
    name: FORGOT_PASSWORD_ROUTE_NAME,
  },
  {
    path: "/reset-password",
    component: () => import("@/pages/ResetPassword.vue"),
    name: RESET_PASSWORD_ROUTE_NAME,
  },
  {
    path: "/",
    redirect: { name: NEWEST_ROUTE_NAME },
    name: HOME_ROUTE_NAME,
  },
  {
    path: "/:uri(.*)*",
    component: () => import("@/pages/NotFound.vue"),
    name: NOT_FOUND_ROUTE_NAME,
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

router.beforeEach(async (to, from) => {
  if (to.hash) {
    return false;
  }
  const userStore = useUserStore();
  try {
    const api = apiClient();
    const { data } = await api.auth.isAuthorized();
    userStore.user = data;
  } catch (error) {
    console.log(error);
  }

  const exceptRoute = [
    NOT_FOUND_ROUTE_NAME,
    LOGIN_ROUTE_NAME,
    REGISTER_ROUTE_NAME,
    NEWEST_ROUTE_NAME,
    FOLLOWINGS_ROUTE_NAME,
    BOOKMARKS_ROUTE_NAME,
    DETAIL_ARTICLE_ROUTE_NAME,
    FORGOT_PASSWORD_ROUTE_NAME,
    RESET_PASSWORD_ROUTE_NAME,
  ];

  if (!userStore.isAuthenticated && !exceptRoute.includes(to.name)) {
    return {
      name: LOGIN_ROUTE_NAME,
      query: { redirect: encodeURIComponent(to.fullPath) },
    };
  }

  if (
    userStore.isAuthenticated &&
    (to.name == REGISTER_ROUTE_NAME || to.name == LOGIN_ROUTE_NAME)
  ) {
    return {
      name: HOME_ROUTE_NAME,
    };
  }
});

export default router;
