<template>
  <div class="sticky top-0 w-full grid grid-cols-2 p-4 shadow-md bg-white">
    <div class="flex items-center">
      <div class="mr-[64px]">
        <img
          src="http://images.viblo.test/images/logo_full.svg"
          alt="Viblo"
          width="62"
          height="21"
        />
      </div>
      <ul
        class="flex items-center gap-x-[40px] font-bold text-gray-400 text-sm"
      >
        <li v-for="(item, index) in dataNaviagtion" :key="index">
          <button class="hover:text-black">{{ item.title }}</button>
        </li>
      </ul>
    </div>
    <div>
      <div class="flex justify-end items-center gap-x-[18px]">
        <button>
          <i class="pi pi-bell"></i>
        </button>
        <button v-tooltip.top="'Viết'">
          <i class="pi pi-pencil"></i>
        </button>
        <div class="cursor-pointer" @click="toggle">
          <Avatar
            image="http://images.viblo.test/images/thumb.jpg"
            class=""
            shape="circle"
          />
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
</script>

<style lang="css" scoped></style>
