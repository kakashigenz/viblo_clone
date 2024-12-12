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
            <span class="text-lg line-clamp-3">{{ article.title }}</span>
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
          <button @click="toggleOptionMenu($event, index)">
            <i class="pi pi-chevron-down" style="font-size: 12px"></i>
          </button>
          <Menu ref="optionMenus" :model="options" :popup="true" />
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
import { Chip, Menu, Paginator } from "primevue";
import { ref, watch } from "vue";
import { onBeforeRouteUpdate, useRoute } from "vue-router";

const route = useRoute();
const status = ref(route.params.status);
const heading = ref("");
const articles = ref([]);
const api = apiClient();
const paginator = ref({
  page: 1,
  size: 10,
  total: 0,
});
const icon = ref("");
const optionMenus = ref();
const options = ref([
  {
    label: "Sửa",
    command() {},
  },
  {
    label: "Xóa",
  },
]);

onBeforeRouteUpdate((to, from) => {
  status.value = to.params.status;
  paginator.value.page = 1;
});

watch(
  status,
  async (newValue) => {
    let data = null;
    if (newValue == "drafts") {
      heading.value = "Bản nháp";
      const res = await api.article.getDraftList();
      data = res.data;
      icon.value = "pi pi-lock";
    } else if (newValue == "public") {
      heading.value = "Công khai";
      const res = await api.article.getPublicList();
      data = res.data;
      icon.value = "pi pi-globe";
    }
    if (data) {
      articles.value = data.data;
      paginator.value.size = data.size;
      paginator.value.total = data.total;
    }
  },
  { immediate: true }
);

watch(
  () => paginator.value.page,
  async (newPage) => {
    let data = null;
    if (status.value == "drafts") {
      heading.value = "Bản nháp";
      const res = await api.article.getDraftList(newPage);
      data = res.data;
      icon.value = "pi pi-lock";
    } else if (status.value == "public") {
      heading.value = "Công khai";
      const res = await api.article.getPublicList(newPage);
      data = res.data;
      icon.value = "pi pi-globe";
    }
    if (data) {
      articles.value = data.data;
      paginator.value.size = data.size;
      paginator.value.total = data.total;
    }
  }
);

const toggleOptionMenu = (e, index) => {
  optionMenus.value[index].toggle(e);
};

const changePage = ({ page }) => {
  paginator.value.page = page + 1;
};
</script>

<style lang="scss" scoped></style>
