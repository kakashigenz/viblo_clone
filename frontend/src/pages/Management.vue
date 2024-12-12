<template>
  <Header container />
  <Container>
    <div class="grid grid-cols-12 py-8 min-h-[480px]">
      <div class="col-span-3">
        <SideBar class="col-span-3">
          <PanelMenu :model="items" />
        </SideBar>
      </div>
      <div class="col-span-9">
        <RouterView />
      </div>
    </div>
  </Container>
  <Footer />
</template>

<script setup>
import Container from "@/components/Container.vue";
import Footer from "@/components/Footer.vue";
import Header from "@/components/Header.vue";
import SideBar from "@/components/SideBar.vue";
import {
  ARTICLE_MANAGEMENT_ROUTE_NAME,
  IMAGE_MANAGEMENT_ROUTE_NAME,
  INFO_MANAGEMENT_ROUTE_NAME,
  PASSWORD_MANAGEMENT_ROUTE_NAME,
} from "@/helper/constant";
import { PanelMenu } from "primevue";
import { ref } from "vue";
import { useRouter } from "vue-router";

const router = useRouter();

const items = ref([
  {
    label: "Bài viết",
    icon: "pi pi-pencil",
    items: [
      {
        label: "Bản nháp",
        icon: "pi pi-lock",
        command() {
          router.push({
            name: ARTICLE_MANAGEMENT_ROUTE_NAME,
            params: { status: "drafts" },
          });
        },
      },
      {
        label: "Công khai",
        icon: "pi pi-globe",
        command() {
          router.push({
            name: ARTICLE_MANAGEMENT_ROUTE_NAME,
            params: { status: "public" },
          });
        },
      },
    ],
  },
  {
    label: "Quản lý hình ảnh",
    icon: "pi pi-image",
    command() {
      router.push({
        name: IMAGE_MANAGEMENT_ROUTE_NAME,
      });
    },
  },
  {
    label: "Thông tin tài khoản",
    icon: "pi pi-id-card",
    items: [
      {
        label: "Thông tin cá nhân",
        icon: "pi pi-user",
        command() {
          router.push({
            name: INFO_MANAGEMENT_ROUTE_NAME,
          });
        },
      },
      {
        label: "Mật khẩu",
        icon: "pi pi-key",
        command() {
          router.push({
            name: PASSWORD_MANAGEMENT_ROUTE_NAME,
          });
        },
      },
    ],
  },
]);
</script>

<style lang="scss" scoped></style>
