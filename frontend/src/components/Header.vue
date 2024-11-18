<template>
  <div class="sticky top-0 w-full p-4 shadow-md bg-white z-[1000]">
    <Container v-if="props.container">
      <div class="grid grid-cols-2">
        <div class="flex items-center">
          <div class="mr-[64px]">
            <RouterLink :to="{ name: 'homePage' }">
              <img
                src="http://images.viblo.test/images/logo_full.svg"
                alt="Viblo"
                width="62"
                height="21"
              />
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
                <RouterLink :to="{ name: action.route }" class="flex items-center">
                  <i :class="`pi ${action.icon}`"></i>
                </RouterLink>
              </li>
            </template>
          </ul>
          <div v-if="userStore.isAuthenticated" class="cursor-pointer" @click="toggle">
            <Avatar
              image="http://images.viblo.test/images/thumb.jpg"
              class=""
              shape="circle"
            />
          </div>
          <div v-else>
            <RouterLink
              :to="{ name: 'login' }"
              class="text-blue-500 flex gap-x-1 items-center text-sm"
            >
              <i class="pi pi-sign-in" style="font-size: 12px"></i>
              <span class="underline">Đăng nhập/Đăng ký</span>
            </RouterLink>
          </div>
          <Popover ref="popover">
            <div>
              <div class="flex gap-x-4 mb-2">
                <div>
                  <Avatar
                    image="http://images.viblo.test/images/thumb.jpg"
                    class=""
                    shape="circle"
                    size="large"
                  />
                </div>
                <div>
                  <div class="flex flex-col text-justify text-sm mb-[14px]">
                    <span class="text-sky-600 font-bold">Quang</span>
                    <span class="text-gray-400"> @kakashigenz </span>
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
              >
                <i class="pi" :class="item.icon"></i>
                <span>{{ item.title }}</span>
              </div>
            </div>
          </Popover>
        </div>
      </div>
    </Container>
    <div v-else class="grid grid-cols-2">
      <div class="flex items-center">
        <div class="mr-[64px]">
          <RouterLink :to="{ name: 'homePage' }">
            <img
              src="http://images.viblo.test/images/logo_full.svg"
              alt="Viblo"
              width="62"
              height="21"
            />
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
          <li v-for="(action, index) in actionMenu" :key="index">
            <RouterLink
              :to="{ name: action.route }"
              v-if="
                (action.requireAuthenticate && userStore.isAuthenticated) ||
                !action.requireAuthenticate
              "
              class="flex items-center"
            >
              <i :class="`pi ${action.icon}`"></i>
            </RouterLink>
          </li>
        </ul>
        <div v-if="userStore.isAuthenticated" class="cursor-pointer" @click="toggle">
          <Avatar
            image="http://images.viblo.test/images/thumb.jpg"
            class=""
            shape="circle"
          />
        </div>
        <div v-else>
          <RouterLink
            :to="{ name: 'login' }"
            class="text-blue-500 flex gap-x-1 items-center text-sm"
          >
            <i class="pi pi-sign-in"></i>
            <span class="underline">Đăng nhập/Đăng ký</span>
          </RouterLink>
        </div>
        <Popover ref="popover">
          <div>
            <div class="flex gap-x-4 mb-2">
              <div>
                <Avatar
                  image="http://images.viblo.test/images/thumb.jpg"
                  class=""
                  shape="circle"
                  size="large"
                />
              </div>
              <div>
                <div class="flex flex-col text-justify text-sm mb-[14px]">
                  <span class="text-sky-600 font-bold">Quang</span>
                  <span class="text-gray-400"> @kakashigenz </span>
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
            >
              <i class="pi" :class="item.icon"></i>
              <span>{{ item.title }}</span>
            </div>
          </div>
        </Popover>
      </div>
    </div>
  </div>
</template>

<script setup>
import Avatar from "primevue/avatar";
import Popover from "primevue/popover";
import { ref } from "vue";
import Container from "./Container.vue";
import { useUserStore } from "@/stores/user";
import SearchBar from "./SearchBar.vue";

const props = defineProps({
  container: Boolean,
});

const userStore = useUserStore();

const popover = ref();
const toggle = (event) => {
  popover.value.toggle(event);
};

const dataUserMenu = ref([
  {
    icon: "pi-user",
    title: "Trang cá nhân",
  },
  {
    icon: "pi-file-edit",
    title: "Quản lý nội dung",
  },
  {
    icon: "pi-sign-out",
    title: "Đăng xuất",
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
  },
  {
    route: "createArticle",
    title: "Viết",
    icon: "pi-pencil",
    requireAuthenticate: true,
  },
]);
</script>

<style lang="css" scoped></style>
