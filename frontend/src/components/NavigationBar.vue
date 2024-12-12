<template>
  <div class="bg-[#0b1a33] py-4">
    <Container class="flex items-center">
      <ul class="flex items-center gap-x-[40px] px-8">
        <li v-for="option in navOptions">
          <RouterLink
            v-if="option.route"
            :to="{ name: option.route }"
            class="block text-white font-bold text-xs text-center uppercase nav-link"
            >{{ option.title }}</RouterLink
          >
          <a
            v-else
            href="#"
            class="block text-white font-bold text-xs uppercase nav-link"
            >{{ option.title }}</a
          >
        </li>
      </ul>
      <div>
        <RouterLink
          :to="{ name: CREATE_ARTICLE_ROUTE_NAME }"
          class="bg-sky-600 text-white rounded-sm px-4 py-1 flex items-center gap-x-2"
        >
          <i class="pi pi-pencil" style="font-size: 14px"></i>
          <span> Viết bài </span>
        </RouterLink>
      </div>
    </Container>
  </div>
</template>

<script setup>
import {
  BOOKMARKS_ROUTE_NAME,
  CREATE_ARTICLE_ROUTE_NAME,
  FOLLOWINGS_ROUTE_NAME,
  NEWEST_ROUTE_NAME,
} from "@/helper/constant";

import Container from "./Container.vue";

const navOptions = [
  {
    title: "Nhà sáng tạo nội dung",
  },
  {
    title: "Đang theo dõi",
    route: FOLLOWINGS_ROUTE_NAME,
  },
  {
    title: "Mới nhất",
    route: NEWEST_ROUTE_NAME,
  },
  {
    title: "Series",
  },

  {
    title: "Editor choice",
  },
  {
    title: "Trending",
  },
  {
    title: "Video",
  },
  {
    title: "Bookmark của tôi",
    route: BOOKMARKS_ROUTE_NAME,
  },
];
</script>

<style lang="css" scoped>
.nav-link::after {
  content: "";
  display: block;
  width: 0;
  height: 2px;
  background-color: #fff;
  transition: all ease 0.4s;
  margin-top: 6px;
}

.nav-link:hover.nav-link::after {
  width: 100%;
}

.router-link-active::after {
  width: 100%;
}
</style>
