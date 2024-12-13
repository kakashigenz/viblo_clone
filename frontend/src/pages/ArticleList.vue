<template>
  <div class="px-6">
    <h5 class="text-xl">{{ heading }}</h5>

    <ul class="mt-4">
      <li
        v-for="(article, index) in articles"
        :key="article.id"
        class="border-b border-gray-300 px-1 py-2"
      >
        <div class="flex items-center gap-x-3 flex-wrap">
          <div class="flex gap-x-1 items-center">
            <i class="text-green-400" :class="icon"></i>
            <RouterLink
              :to="{ name: DETAIL_ARTICLE_ROUTE_NAME, params: { slug: article.slug } }"
            >
              <span class="text-lg line-clamp-3">{{ article.title }}</span>
            </RouterLink>
          </div>
          <Chip
            v-for="tag in article.tags"
            :key="tag.id"
            :label="tag.name"
            :dt="{
              background: '{gray-200}',
              'padding.y': '4px',
            }"
          />
        </div>
        <div class="flex items-center gap-x-1">
          <span class="text-sm text-gray-400">
            {{ `Sửa đổi lần cuối ${getFormatedTime(article.updated_at)}` }}</span
          >
          <button @click="toggleOptionMenu($event, article.slug)">
            <i class="pi pi-chevron-down" style="font-size: 12px"></i>
          </button>

          <Menu
            :model="options(article.slug)"
            :ref="
              (el) => {
                optionMenus.set(article.slug, el);
              }
            "
            :popup="true"
          />
        </div>
      </li>
    </ul>
    <Paginator
      v-if="paginator.size < paginator.total"
      :rows="paginator.size"
      :totalRecords="paginator.total"
      template="PrevPageLink PageLinks NextPageLink"
      @page="changePage"
    />
  </div>
</template>

<script setup>
import apiClient from "@/api";
import { getFormatedTime } from "@/helper";
import { DETAIL_ARTICLE_ROUTE_NAME, EDIT_ARTICLE_ROUTE_NAME } from "@/helper/constant";
import { Chip, Menu, Paginator, Popover, useConfirm, useToast } from "primevue";
import { nextTick, ref, watch } from "vue";
import { onBeforeRouteUpdate, useRoute, useRouter } from "vue-router";

const route = useRoute();
const router = useRouter();
const heading = ref("");
const articles = ref([]);
const api = apiClient();
const paginator = ref({
  page: 1,
  size: 10,
  total: 0,
});
const icon = ref("");
const optionMenus = new Map();
const toast = useToast();
const confirm = useConfirm();

const options = (key) => {
  return [
    {
      label: "Sửa",
      command: () => {
        router.push({ name: EDIT_ARTICLE_ROUTE_NAME, params: { slug: key } });
      },
    },
    {
      label: "Xóa",
      command() {
        confirm.require({
          header: "Xóa bài viết",
          message: "Bạn có thực sự muốn xóa bài viết này",
          rejectProps: {
            label: "Hủy",
            severity: "secondary",
            outlined: true,
          },
          acceptProps: {
            label: "Xóa",
            severity: "danger",
          },
          accept: async () => {
            const { status: resStatus } = await api.article.delete(key);
            if (resStatus == 200) {
              toast.add({
                severity: "success",
                summary: "Thông báo",
                detail: "Xóa thành công",
                life: 2000,
              });
              loadArticles(route.params.status, paginator.value.page);
            }
          },
        });
      },
    },
  ];
};

onBeforeRouteUpdate((to, from) => {
  paginator.value.page = 1;
});

async function loadArticles(mode, page = 1) {
  let data = undefined;
  if (mode == "drafts") {
    heading.value = "Bản nháp";
    const res = await api.article.getDraftList(page);
    data = res.data;
    icon.value = "pi pi-lock";
  } else if (mode == "public") {
    heading.value = "Công khai";
    const res = await api.article.getPublicList(page);
    data = res.data;
    icon.value = "pi pi-globe";
  }
  if (data) {
    articles.value = data.data;
    paginator.value.size = data.size;
    paginator.value.total = data.total;
    console.log(optionMenus);
  }
}

watch(
  () => route.params.status,
  async (newValue) => {
    await loadArticles(newValue);
  },
  { immediate: true }
);

watch(
  () => paginator.value.page,
  async (newPage) => {
    await loadArticles(route.params.status, newPage);
  }
);

const toggleOptionMenu = (e, key) => {
  optionMenus.get(key).toggle(e);
};

const changePage = ({ page }) => {
  paginator.value.page = page + 1;
};
</script>

<style lang="scss" scoped></style>
