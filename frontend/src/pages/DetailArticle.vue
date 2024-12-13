<template>
  <Header container />
  <Banner />
  <Container>
    <div v-if="article">
      <div ref="main" class="grid grid-cols-12 mt-10 py-[32px]">
        <div class="col-span-1 pr-[8px]">
          <SideBar class="mt-[84px]">
            <div class="flex flex-col items-center">
              <a href="#" class="mb-2">
                <Avatar
                  v-if="showSidebarAvatar"
                  :image="getURLAvatar(article?.user)"
                  shape="circle"
                  size="large"
                />
              </a>
              <button @click="upvote" class="flex items-end text-gray-500">
                <i
                  class="pi pi-sort-up-fill"
                  :class="article.vote_type == UPVOTE_TYPE ? 'text-sky-600' : ''"
                  style="font-size: 28px"
                ></i>
              </button>
              <span class="text-xl text-gray-500">{{ article.point }}</span>
              <button @click="downvote" class="mb-4 text-gray-500">
                <i
                  class="pi pi-sort-down-fill"
                  :class="article.vote_type == DOWNVOTE_TYPE ? 'text-sky-600' : ''"
                  style="font-size: 28px"
                ></i>
              </button>
              <button
                class="w-[40px] h-[40px] border-2 border-gray-400 rounded-full flex items-center justify-center"
              >
                <i class="pi pi-bookmark text-gray-500"></i>
              </button>
            </div>
          </SideBar>
        </div>
        <div class="col-span-8 pr-[15px]">
          <div class="flex">
            <div class="w-1/2 flex">
              <div class="mr-3">
                <Avatar :image="getURLAvatar(article.user)" shape="circle" size="large" />
              </div>
              <div>
                <div class="flex items-center gap-x-2">
                  <a
                    href="#"
                    class="text-sm text-sky-500 hover:underline hover:text-sky-600"
                  >
                    {{ article.user.name }}
                  </a>
                  <span class="text-sm text-gray-400">{{
                    `@${article.user.user_name}`
                  }}</span>
                  <Button variant="secondary" size="small">Theo dõi</Button>
                </div>
                <div class="flex gap-x-3">
                  <span class="flex items-center gap-x-2">
                    <i class="pi pi-user-plus" style="font-size: 14px"></i>
                    <span class="leading-4">{{ article.user.followings_count }}</span>
                  </span>
                  <span class="flex items-center gap-x-1">
                    <i class="pi pi-pencil" style="font-size: 14px"></i>
                    <span class="leading-4">{{ article.user.articles_count }}</span>
                  </span>
                </div>
              </div>
            </div>
            <div class="w-1/2 flex flex-col items-end">
              <p class="text-sm text-gray-400 mb-1">
                {{ getFormatedTime(article.created_at) }}
              </p>
              <div class="flex gap-x-2">
                <span class="flex items-center gap-x-1 text-gray-500">
                  <i class="pi pi-comments" style="font-size: 20px"></i>
                  <span class="leading-4">{{ article.comments_count }}</span>
                </span>
                <span class="flex items-center gap-x-1 text-gray-500">
                  <i class="pi pi-bookmark" style="font-size: 20px"></i>
                  <span class="leading-4">{{ article.bookmarks_count }}</span>
                </span>
              </div>
            </div>
          </div>
          <div class="mt-4">
            <h1 class="font-bold text-4xl line-clamp-3">
              {{ article.title }}
            </h1>
            <div
              class="md-contents mt-4 flex gap-y-4 flex-col text-lg"
              @click="copyCode"
              v-html="markdownContent"
            ></div>
          </div>
          <div class="mt-[32px] flex gap-x-2 flex-wrap">
            <a href="#" v-for="tag in article.tags">
              <Chip
                :key="tag.id"
                :label="tag.name"
                class="text-xs"
                :dt="{
                  background: '{gray.200}',
                }"
              />
            </a>
          </div>
        </div>
        <div class="col-span-3">
          <SideBar class="overflow-hidden">
            <div class="mt-1">
              <header class="flex">
                <h5 class="uppercase text-gray-600 leading-4 mr-4">Mục lục</h5>
                <div class="border-b border-gray-300 flex-1"></div>
              </header>
              <div class="toc-contents mt-2 py-2"></div>
            </div>
          </SideBar>
        </div>
      </div>
      <div>
        <h6 class="text-lg font-bold mb-6">Bình luận</h6>
        <CommentList class="my-5" />
      </div>
    </div>
    <div v-else-if="isFetching" ref="main" class="grid grid-cols-12 pt-[32px] pb-[16px]">
      <div class="col-span-1 pr-[8px]">
        <SideBar class="mt-[84px]">
          <div class="flex flex-col items-center">
            <Skeleton width="3rem" height="11rem"></Skeleton>
          </div>
        </SideBar>
      </div>
      <div class="col-span-8 pr-[15px]">
        <div class="flex">
          <div class="w-1/2 flex">
            <div class="mr-3">
              <Skeleton size="3rem" shape="circle"></Skeleton>
            </div>
            <div>
              <div class="flex items-center gap-x-2">
                <Skeleton v-for="i in 3" width="3rem" height="1rem"></Skeleton>
              </div>
              <div class="flex gap-x-3 mt-2">
                <span class="flex items-center gap-x-2">
                  <Skeleton width="3rem" height="1rem"></Skeleton>
                </span>
                <span class="flex items-center gap-x-1">
                  <Skeleton width="3rem" height="1rem"></Skeleton>
                </span>
              </div>
            </div>
          </div>
          <div class="w-1/2 flex flex-col items-end">
            <p class="text-sm text-gray-400 mb-1">
              <Skeleton width="11rem" height="1rem"></Skeleton>
            </p>
            <div class="flex gap-x-2">
              <span class="flex items-center gap-x-1 text-gray-500">
                <Skeleton width="3rem" height="1rem"></Skeleton>
              </span>
              <span class="flex items-center gap-x-1 text-gray-500">
                <Skeleton width="3rem" height="1rem"></Skeleton>
              </span>
            </div>
          </div>
        </div>
        <div class="mt-4">
          <h3 class="font-bold text-3xl line-clamp-3">
            <Skeleton v-for="i in 25" width="100%" height="1rem" class="mb-2"></Skeleton>
          </h3>
          <p class="mt-4 flex gap-y-4 flex-col text-lg"></p>
        </div>
      </div>
      <div class="col-span-3">
        <SideBar class="overflow-hidden">
          <Skeleton v-for="i in 7" width="100%" height="1rem" class="mb-2"></Skeleton>
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
import SideBar from "@/components/SideBar.vue";
import { getFormatedTime, getURLAvatar } from "@/helper";
import { Chip, Skeleton, useToast } from "primevue";
import Avatar from "primevue/avatar";
import { inject, nextTick, onBeforeUnmount, onMounted, ref, watch } from "vue";
import { useRoute } from "vue-router";
import tocbot from "tocbot";
import CommentList from "@/components/CommentList.vue";
import { DOWNVOTE_TYPE, UPVOTE_TYPE } from "@/helper/constant";
import { useUserStore } from "@/stores/user";

const showSidebarAvatar = ref(false);
const main = ref();
const isFetching = ref(false);
const article = ref();
const api = apiClient();
const route = useRoute();
const md = inject("md");
const markdownContent = ref("");
const userStore = useUserStore();
const toast = useToast();

const handleScroll = (e) => {
  const top = main.value?.getBoundingClientRect().top;
  if (top <= 0) {
    showSidebarAvatar.value = true;
  } else {
    showSidebarAvatar.value = false;
  }
};

onMounted(async () => {
  window.scrollTo(0, 0);
  window.addEventListener("scroll", handleScroll);

  isFetching.value = true;
  const { slug } = route.params;
  try {
    const { data } = await api.article.getObject(slug);
    article.value = data;
    markdownContent.value = md.parse(article.value.content);
    nextTick(() => {
      tocbot.init({
        tocSelector: ".toc-contents",
        contentSelector: ".md-contents",
        headingSelector: "h1,h2,h3",
        headingsOffset: 86,
        hasInnerContainers: true,
        scrollSmoothOffset: -86,
      });
    });
  } catch (error) {
    console.log(error);
  }
  isFetching.value = false;
});

onBeforeUnmount(() => {
  window.removeEventListener("scroll", handleScroll);
});

const copyCode = (event) => {
  if (event.target.classList.contains("copy-code-btn")) {
    try {
      const code = event.target.dataset.code;

      const textarea = document.createElement("textarea");
      textarea.value = code;
      textarea.style.display = "none";
      document.body.appendChild(textarea);
      textarea.select();
      document.execCommand("copy");
      event.target.classList.remove("pi-clipboard");
      event.target.classList.add("pi-check");
      setTimeout(() => {
        event.target.classList.remove("pi-check");
        event.target.classList.add("pi-clipboard");
      }, 1500);
    } catch (error) {
      throw error;
    }
  }
};

const upvote = async () => {
  if (!userStore.isAuthenticated) {
    toast.add({
      severity: "error",
      summary: "Vui lòng đăng nhập để thực hiện",
      life: 3000,
    });
    return;
  }
  try {
    const { data } = await api.article.upvote(article.value.id);
    article.value.vote_type = data.vote_type;
    article.value.point = data.value;
  } catch (error) {
    console.log(error);
  }
};

const downvote = async () => {
  if (!userStore.isAuthenticated) {
    toast.add({
      severity: "error",
      summary: "Vui lòng đăng nhập để thực hiện",
      life: 3000,
    });
    return;
  }
  try {
    const { data } = await api.article.downvote(article.value.id);
    article.value.vote_type = data.vote_type;
    article.value.point = data.value;
  } catch (error) {
    console.log(error);
  }
};
</script>

<style lang="scss" scoped></style>
