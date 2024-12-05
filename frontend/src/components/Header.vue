<template>
  <header
    class="sticky top-0 w-full p-4 shadow-md bg-white"
    :class="props.container ? 'z-[1000]' : ''"
  >
    <Container v-if="props.container">
      <div class="grid grid-cols-2">
        <div class="flex items-center">
          <div class="mr-[64px]">
            <RouterLink :to="{ name: HOME_ROUTE_NAME }">
              <img src="/images/logo_full.svg" alt="Viblo" width="62" height="21" />
            </RouterLink>
          </div>
          <ul class="flex items-center gap-x-[40px] font-bold text-gray-400 text-sm">
            <li v-for="(item, index) in dataNaviagtion" :key="index">
              <button class="hover:text-black">{{ item.title }}</button>
            </li>
          </ul>
        </div>
        <div class="flex justify-end items-center">
          <SearchBar class="mr-8 flex-1" />
          <ul class="flex items-center">
            <template v-for="(action, index) in actionMenu" :key="index">
              <li
                v-if="
                  (action.requireAuthenticate && userStore.isAuthenticated) ||
                  !action.requireAuthenticate
                "
                class="mr-4"
              >
                <RouterLink
                  v-if="action.route"
                  :to="{ name: action.route }"
                  class="flex items-center"
                >
                  <i :class="`pi ${action.icon}`"></i>
                </RouterLink>
                <button v-else class="flex items-center">
                  <OverlayBadge
                    v-if="action.badgeValue"
                    :value="action.badgeValue"
                    size="small"
                    severity="danger"
                    class="flex items-center"
                  >
                    <i :class="`pi ${action.icon}`"></i>
                  </OverlayBadge>
                  <i v-else :class="`pi ${action.icon}`"></i>
                </button>
              </li>
            </template>
          </ul>
          <div
            v-if="userStore.isAuthenticated"
            class="cursor-pointer flex items-center"
            @click="toggle"
          >
            <Avatar :image="getURLAvatar(userStore.user)" class="" shape="circle" />
          </div>
          <div v-else>
            <RouterLink
              :to="{
                name: LOGIN_ROUTE_NAME,
                query: { redirect: encodeURIComponent(route.fullPath) },
              }"
              class="text-blue-500 flex gap-x-1 items-center text-sm"
            >
              <i class="pi pi-sign-in" style="font-size: 12px"></i>
              <span class="underline">Đăng nhập/Đăng ký</span>
            </RouterLink>
          </div>
          <Popover v-if="userStore.isAuthenticated" ref="popover">
            <div>
              <div class="flex gap-x-4 mb-2">
                <div>
                  <Avatar
                    :image="getURLAvatar(userStore.user)"
                    class=""
                    shape="circle"
                    size="large"
                  />
                </div>
                <div>
                  <div class="flex flex-col text-justify text-sm mb-[14px]">
                    <span class="text-sky-600 font-bold">{{ userStore.user?.name }}</span>
                    <span class="text-gray-400"> {{ `@${userStore.user?.name}` }} </span>
                  </div>
                  <div>
                    <button
                      class="px-3 py-[6px] bg-sky-600 text-white font-medium rounded-md text-sm hover:bg-opacity-70"
                    >
                      Sửa
                    </button>
                  </div>
                </div>
              </div>
              <div
                v-for="(item, index) in dataUserMenu"
                :key="index"
                class="px-2 py-3 text-gray-600 flex items-center gap-x-4 cursor-pointer hover:bg-gray-200 rounded-md"
                @click="item.handle"
              >
                <i class="pi" :class="item.icon"></i>
                <span>{{ item.title }}</span>
              </div>
            </div>
          </Popover>
        </div>
      </div>
    </Container>
    <div v-else class="grid grid-cols-2 z-0">
      <div class="flex items-center">
        <div class="mr-[64px]">
          <RouterLink :to="{ name: HOME_ROUTE_NAME }">
            <img src="/images/logo_full.svg" alt="Viblo" width="62" height="21" />
          </RouterLink>
        </div>
        <ul class="flex items-center gap-x-[40px] font-bold text-gray-400 text-sm">
          <li v-for="(item, index) in dataNaviagtion" :key="index">
            <button class="hover:text-black">{{ item.title }}</button>
          </li>
        </ul>
      </div>
      <div class="flex justify-end items-center gap-x-[18px]">
        <ul class="flex items-center gap-x-[18px]">
          <template v-for="(action, index) in actionMenu" :key="index">
            <li
              v-if="
                (action.requireAuthenticate && userStore.isAuthenticated) ||
                !action.requireAuthenticate
              "
              class="mr-4"
            >
              <RouterLink
                v-if="action.route"
                :to="{ name: action.route }"
                class="flex items-center"
              >
                <i :class="`pi ${action.icon}`"></i>
              </RouterLink>
              <button v-else class="flex items-center">
                <i :class="`pi ${action.icon}`"></i>
              </button>
            </li>
          </template>
        </ul>
        <div
          v-if="userStore.isAuthenticated"
          class="cursor-pointer flex items-center"
          @click="toggle"
        >
          <Avatar :image="getURLAvatar(userStore.user)" class="" shape="circle" />
        </div>
        <div v-else>
          <RouterLink
            :to="{
              name: LOGIN_ROUTE_NAME,
              query: { redirect: encodeURIComponent(route.fullPath) },
            }"
            class="text-blue-500 flex gap-x-1 items-center text-sm"
          >
            <i class="pi pi-sign-in"></i>
            <span class="underline">Đăng nhập/Đăng ký</span>
          </RouterLink>
        </div>
        <Popover v-if="userStore.isAuthenticated" ref="popover">
          <ul>
            <li class="flex gap-x-4 mb-2">
              <div>
                <Avatar
                  :image="getURLAvatar(userStore.user)"
                  class=""
                  shape="circle"
                  size="large"
                />
              </div>
              <div>
                <div class="flex flex-col text-justify text-sm mb-[14px]">
                  <span class="text-sky-600 font-bold">{{ userStore.user?.name }}</span>
                  <span class="text-gray-400">
                    {{ `@${userStore.user?.user_name}` }}
                  </span>
                </div>
                <div>
                  <button
                    class="px-3 py-[6px] bg-sky-600 text-white font-medium rounded-md text-sm hover:bg-opacity-70"
                  >
                    Sửa
                  </button>
                </div>
              </div>
            </li>
            <div
              v-for="(item, index) in dataUserMenu"
              :key="index"
              class="px-2 py-3 text-gray-600 flex items-center gap-x-4 cursor-pointer hover:bg-gray-200 rounded-md"
              @click="item.handle"
            >
              <i class="pi" :class="item.icon"></i>
              <span>{{ item.title }}</span>
            </div>
          </ul>
        </Popover>
      </div>
    </div>
  </header>
</template>

<script setup>
import Avatar from "primevue/avatar";
import Popover from "primevue/popover";
import { ref } from "vue";
import Container from "./Container.vue";
import { useUserStore } from "@/stores/user";
import SearchBar from "./SearchBar.vue";
import { useRoute, useRouter } from "vue-router";
import {
  CREATE_ARTICLE_ROUTE_NAME,
  HOME_ROUTE_NAME,
  LOGIN_ROUTE_NAME,
} from "@/helper/constant";
import { OverlayBadge } from "primevue";

const props = defineProps({
  container: Boolean,
});

const userStore = useUserStore();

const router = useRouter();
const route = useRoute();
const popover = ref();
const toggle = (event) => {
  popover.value.toggle(event);
};

const dataUserMenu = ref([
  {
    icon: "pi-user",
    title: "Trang cá nhân",
    handle: () => {},
  },
  {
    icon: "pi-file-edit",
    title: "Quản lý nội dung",
    handle: () => {},
  },
  {
    icon: "pi-sign-out",
    title: "Đăng xuất",
    handle: async () => {
      await userStore.logout();
      if (!userStore.user) {
        router.push({ name: HOME_ROUTE_NAME });
      }
    },
  },
]);

const dataNaviagtion = ref([
  {
    link: "#",
    title: "Bài viết",
  },
  {
    link: "#",
    title: "Thảo luận",
  },
]);

const actionMenu = ref([
  {
    icon: "pi-bell",
    requireAuthenticate: true,
    badgeValue: 2,
  },
  {
    route: CREATE_ARTICLE_ROUTE_NAME,
    title: "Viết",
    icon: "pi-pencil",
    requireAuthenticate: true,
  },
]);

const getURLAvatar = (user) => {
  return (
    user.avatar ?? `https://placehold.co/45x45/green/FFF?text=${user.name.slice(0, 1)}`
  );
};
</script>

<style lang="css" scoped></style>
