<template>
  <Header container />
  <Banner />
  <NavigationBar />
  <Container>
    <div class="grid grid-cols-12 pt-[32px] pb-[16px] overflow-visible">
      <div class="col-span-9 pr-[15px]">
        <!-- watch mode -->
        <div v-if="!isFetching" class="flex justify-end items-center gap-x-6">
          <i
            class="pi pi-list cursor-pointer"
            @click="watchMode = WATCH_SHORT"
            style="font-size: 20px"
            :class="watchMode == WATCH_SHORT ? 'text-sky-500' : ''"
          ></i>
          <i
            class="pi pi-align-left cursor-pointer"
            @click="watchMode = WATCH_DETAIL"
            :class="watchMode == WATCH_DETAIL ? 'text-sky-500' : ''"
            style="font-size: 20px"
          ></i>
        </div>
        <!-- loading -->
        <div v-else class="flex justify-end items-center gap-x-6">
          <Skeleton width="2.2rem" height="1.5rem" class="mb-2"></Skeleton>
          <Skeleton width="2.2rem" height="1.5rem" class="mb-2"></Skeleton>
        </div>
        <!-- data -->
        <ListArticle :data="articles" :watchMode="watchMode" :isFetching="isFetching" />
        <Paginator
          v-if="articles.length < paginator.total"
          v-model:rows="paginator.size"
          :totalRecords="paginator.total"
          template="PrevPageLink PageLinks NextPageLink"
          @page="changePage"
        />
      </div>
      <div class="col-span-3">
        <SideBar class="overflow-y-auto">
          <div class="mt-1">
            <header class="flex">
              <a href="#">
                <h5
                  class="uppercase text-sky-500 hover:text-sky-700 border-sky-600 hover:border-b leading-4 mr-2"
                >
                  Các tác giả hàng đầu
                </h5>
              </a>
              <div class="border-b border-gray-300 flex-1"></div>
            </header>
            <div class="flex flex-col gap-y-1 py-2">
              <div
                v-for="user in topUsers"
                :key="user.id"
                class="py-2 flex flex-col gap-y-2"
              >
                <div class="flex gap-x-5">
                  <a href="#">
                    <Avatar :image="getURLAvatar(user)" size="xlarge" shape="circle" />
                  </a>
                  <div class="flex flex-col gap-y-2">
                    <div>
                      <a
                        href="#"
                        class="text-sm text-sky-500 hover:underline hover:text-sky-600"
                        >{{ user.name }}</a
                      >
                      <p class="text-sm">{{ `@${user.user_name}` }}</p>
                    </div>
                    <div>
                      <Button variant="secondary" size="small">
                        <i class="pi pi-plus" style="font-size: 12px"></i>
                        Theo dõi
                      </Button>
                    </div>
                  </div>
                </div>
                <div class="px-2 flex items-center gap-x-4">
                  <span class="cursor-default" v-tooltip.bottom="'Bài viết'">
                    <i class="pi pi-book mr-1" style="font-size: 12px"></i>
                    <span>{{ user.articles_count }}</span>
                  </span>
                  <span class="cursor-default" v-tooltip.bottom="'Người theo dõi'">
                    <i class="pi pi-user-plus mr-1" style="font-size: 14px"></i>
                    <span>{{ user.followers_count }}</span>
                  </span>
                </div>
              </div>
            </div>
          </div>
          <div>
            <header class="flex">
              <a href="#">
                <h5
                  class="uppercase text-sky-500 hover:text-sky-700 border-sky-600 hover:border-b leading-4 mr-2"
                >
                  Các chủ đề hàng đầu
                </h5>
              </a>
              <div class="border-b border-gray-300 flex-1"></div>
            </header>
            <div class="flex flex-col gap-y-1 py-2">
              <div
                v-for="tag in topTags"
                :key="tag.id"
                class="py-2 flex flex-col gap-y-2"
              >
                <div class="flex gap-x-5">
                  <a href="#">
                    <Avatar
                      :image="getPlaceHoldAvatar(tag.name)"
                      size="xlarge"
                      shape="circle"
                    />
                  </a>
                  <div class="flex flex-col gap-y-2">
                    <div>
                      <a
                        href="#"
                        class="text-sm text-sky-500 hover:underline hover:text-sky-600"
                        >{{ tag.name }}</a
                      >
                      <p class="text-sm">{{ `@${tag.name}` }}</p>
                    </div>
                    <div>
                      <Button variant="secondary" size="small">
                        <i class="pi pi-plus" style="font-size: 12px"></i>
                        Theo dõi
                      </Button>
                    </div>
                  </div>
                </div>
                <div class="px-2 flex items-center gap-x-4">
                  <span class="cursor-default" v-tooltip.bottom="'Bài viết'">
                    <i class="pi pi-book mr-1" style="font-size: 12px"></i>
                    <span>{{ tag.articles_count }}</span>
                  </span>
                  <span class="cursor-default" v-tooltip.bottom="'Người theo dõi'">
                    <i class="pi pi-user-plus mr-1" style="font-size: 14px"></i>
                    <span>{{ tag.followers_count }}</span>
                  </span>
                </div>
              </div>
            </div>
          </div>
        </SideBar>
      </div>
    </div>
  </Container>
  <Footer />
</template>

<script setup>
import apiClient from "@/api";
import Banner from "@/components/Banner.vue";
import Button from "@/components/Button.vue";
import Container from "@/components/Container.vue";
import Footer from "@/components/Footer.vue";
import Header from "@/components/Header.vue";
import ListArticle from "@/components/ListArticle.vue";
import NavigationBar from "@/components/NavigationBar.vue";
import SideBar from "@/components/SideBar.vue";
import { getPlaceHoldAvatar, getURLAvatar } from "@/helper";
import { WATCH_DETAIL, WATCH_SHORT } from "@/helper/constant";
import { Avatar, Paginator, Skeleton } from "primevue";
import { ref, watch, onBeforeMount } from "vue";

const isFetching = ref(false);
const paginator = ref({
  size: 20,
  total: 0,
  currentPage: 1,
});
const api = apiClient();
const articles = ref([]);
const watchMode = ref(WATCH_DETAIL);
const topUsers = ref([]);
const topTags = ref([]);

onBeforeMount(async () => {
  isFetching.value = true;
  try {
    const res = await Promise.all([api.user.getTopUser(), api.tag.getTopTag()]);
    // const { data } = await api.user.getTopUser();
    topUsers.value = res[0].data.data;
    topTags.value = res[1].data.data;
  } catch (error) {
    console.log(error);
  }
  isFetching.value = false;
});

watch(
  () => paginator.value.currentPage,
  async (newValue) => {
    isFetching.value = true;
    window.scrollTo({ top: 0, behavior: "smooth" });
    try {
      const { data } = await api.article.getList(newValue);
      articles.value = data.data;
      paginator.value.total = data.total;
      paginator.value.size = data.size;
    } catch (error) {
      console.log(error);
    }
    isFetching.value = false;
  },
  { immediate: true }
);

const changePage = (event) => {
  paginator.value.currentPage = event.page + 1;
};
</script>

<style lang="scss" scoped></style>
