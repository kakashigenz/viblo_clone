<template>
  <header
    class="sticky top-0 w-full px-4 py-3 shadow-md bg-white"
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
          <ul v-if="userStore.isAuthenticated" class="flex items-center">
            <li class="mr-4">
              <button @click="toggleNotification" class="flex items-center px-3">
                <OverlayBadge
                  v-if="unreadCount"
                  size="small"
                  severity="danger"
                  class="flex items-center"
                  :value="unreadCount"
                >
                  <i :class="`pi pi-bell`"></i>
                </OverlayBadge>
                <i v-else :class="`pi pi-bell`"></i>
              </button>
              <Popover ref="notification">
                <div class="w-[380px]">
                  <div class="border-b border-gray-300 py-1 flex justify-between">
                    <h5 class="">Thông báo</h5>
                    <button @click="markAllRead" class="text-xs hover:underline">
                      Đánh dấu tất cả đã đọc
                    </button>
                  </div>
                  <ul class="max-h-[360px] overflow-y-auto" style="scrollbar-width: thin">
                    <li
                      v-for="notification in notifications"
                      :key="notification.id"
                      class="border-b border-gray-300 last:border-b-0"
                    >
                      <a
                        href="#"
                        @click="markAsRead(notification.id)"
                        class="flex items-center gap-x-4 py-2 px-1"
                        :class="notification.read_at ? '' : 'bg-gray-200'"
                      >
                        <img
                          :src="
                            getURLAvatar({
                              avatar: notification.avatar,
                              name: notification.name,
                            })
                          "
                          alt="Avatar"
                          class="w-10 h-10 object-cover rounded-full"
                        />
                        <div>
                          <p class="text-sm line-clamp-3 mb-1 flex gap-x-2 items-center">
                            <span class="text-sky-600 font-bold">{{
                              notification.name
                            }}</span>
                            <span>{{ notification.notification }}</span>
                          </p>
                          <p class="text-xs">
                            {{ getFormatedTime(notification.created_at) }}
                          </p>
                        </div>
                      </a>
                    </li>
                  </ul>
                  <div class="flex justify-center border-t pt-2 border-gray-300">
                    <button v-if="paginator.hasNext" @click="loadMoreNotification">
                      Xem thêm
                    </button>
                  </div>
                </div>
              </Popover>
            </li>
            <li class="mr-4">
              <RouterLink
                :to="{ name: CREATE_ARTICLE_ROUTE_NAME }"
                class="flex items-center"
              >
                <i :class="`pi pi-pencil`"></i>
              </RouterLink>
            </li>
          </ul>
          <div
            v-if="userStore.isAuthenticated"
            class="cursor-pointer flex items-center px-1"
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
          <Popover v-if="userStore.isAuthenticated" ref="userMenu">
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
      <div class="flex justify-end items-center">
        <ul v-if="userStore.isAuthenticated" class="flex items-center">
          <li class="mr-4">
            <button class="flex items-center">
              <OverlayBadge
                size="small"
                severity="danger"
                class="flex items-center"
                value="2"
              >
                <i :class="`pi pi-bell`"></i>
              </OverlayBadge>
            </button>
          </li>
          <li class="mr-4">
            <RouterLink
              :to="{ name: CREATE_ARTICLE_ROUTE_NAME }"
              class="flex items-center"
            >
              <i :class="`pi pi-pencil`"></i>
            </RouterLink>
          </li>
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
        <Popover v-if="userStore.isAuthenticated" ref="userMenu">
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
import { onMounted, ref } from "vue";
import Container from "./Container.vue";
import { useUserStore } from "@/stores/user";
import SearchBar from "./SearchBar.vue";
import { useRoute, useRouter } from "vue-router";
import {
  ARTICLE_MANAGEMENT_ROUTE_NAME,
  CREATE_ARTICLE_ROUTE_NAME,
  HOME_ROUTE_NAME,
  LOGIN_ROUTE_NAME,
} from "@/helper/constant";
import { OverlayBadge, useToast } from "primevue";
import { getFormatedTime, getURLAvatar } from "@/helper";
import apiClient from "@/api";

const props = defineProps({
  container: Boolean,
});

const userStore = useUserStore();
const router = useRouter();
const route = useRoute();
const userMenu = ref();
const notification = ref();
const api = apiClient();
const toggle = (event) => {
  userMenu.value.toggle(event);
};
const notifications = ref([]);
const paginator = ref({
  page: 1,
  hasNext: false,
});
const unreadCount = ref(0);
const toast = useToast();

onMounted(async () => {
  if (userStore.isAuthenticated) {
    window.Echo.private(`App.Models.User.${userStore.user.id}`).notification(
      (notification) => {
        toast.add({
          severity: "info",
          summary: "Thông báo",
          detail: "Bạn có thông báo mới",
          life: 2000,
        });
        notifications.value.unshift(notification);
        unreadCount.value++;
      }
    );
  }
  try {
    const { data } = await api.notification.getList(paginator.value.page);
    notifications.value = data.data;
    paginator.value.hasNext = data.has_next;
    unreadCount.value = data.unread_count;
    paginator.value.page++;
  } catch (error) {
    console.log(error);
  }
});

const dataUserMenu = ref([
  {
    icon: "pi-user",
    title: "Trang cá nhân",
    handle: () => {},
  },
  {
    icon: "pi-file-edit",
    title: "Quản lý tài khoản",
    handle: () => {
      router.push({ name: ARTICLE_MANAGEMENT_ROUTE_NAME, params: { status: "draft" } });
    },
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

const toggleNotification = (event) => {
  notification.value.toggle(event);
};

const markAllRead = async () => {
  try {
    const { data, status } = await api.notification.markAllRead();
    if (status == 200 && data.message == "success") {
      notifications.value.forEach((item) => {
        item.read_at = true;
      });
      unreadCount.value = 0;
    }
  } catch (error) {
    console.log(error);
  }
};

const markAsRead = async (id) => {
  try {
    const { data, status } = await api.notification.markAasRead(id);
    if (status == 200 && data.message == "success") {
      const index = notifications.value.findIndex((item) => item.id == id);
      notifications.value[index].read_at = true;
      unreadCount.value--;
    }
  } catch (error) {
    console.log(error);
  }
};

const loadMoreNotification = async () => {
  if (paginator.value.hasNext) {
    try {
      const { data } = await api.notification.getList(paginator.value.page);
      notifications.value = [...notifications.value, ...data.data];
      paginator.value.hasNext = data.has_next;
      unreadCount.value = data.unread_count;
      paginator.value.page++;
    } catch (error) {
      console.log(error);
    }
  }
};
</script>

<style lang="css" scoped></style>
