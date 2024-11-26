<template>
  <div class="relative">
    <div class="relative">
      <input
        type="text"
        class="pl-[18px] pr-[72px] block w-full py-2 border-2 border-gray-300 outline-sky-600 rounded-tl-sm rounded-bl-sm text-sm"
        placeholder="Tìm kiếm trên Viblo"
        v-model="searchKeyword"
        @blur="isOpenDropdown = false"
      />
      <div
        v-if="isFetching"
        class="absolute right-[56px] top-1/2 -translate-y-1/2 flex items-center"
      >
        <i class="pi pi-spin pi-spinner"></i>
      </div>
      <button
        v-else-if="searchKeyword"
        @click="clearSearch"
        class="absolute right-[56px] top-1/2 -translate-y-1/2 flex items-center"
      >
        <i class="pi pi-times"></i>
      </button>
    </div>
    <button
      class="p-3 bg-sky-600 absolute top-0 right-0 flex justify-center items-center"
    >
      <i class="pi pi-search" style="color: #fff"></i>
    </button>
    <!-- drop down -->
    <div
      v-if="isOpenDropdown"
      class="absolute top-full w-full border border-gray-200 bg-white p-2 max-h-[calc(100vh-80px)] overflow-auto"
    >
      <div v-if="hasResults">
        <div v-if="results?.articles.length" class="mb-2 last:mb-0">
          <h5 class="bg-sky-600 p-3 font-bold text-sm uppercase text-white">Bài viết</h5>
          <ul>
            <li
              v-for="article in results?.articles"
              :key="article.id"
              class="p-2 border-b last:border-none border-dashed border-gray-200 hover:bg-gray-200"
            >
              <h6 v-html="article.title" class="font-bold text-sm"></h6>
              <p class="text-sm">
                <span class="text-sky-600 text-sm">{{ article?.user?.name }}</span>
                <span class="text-gray-400 text-sm">{{
                  ` được tạo lúc ${printDate(article.created_at)}`
                }}</span>
              </p>
              <p class="text-sm" v-html="article.content"></p>
            </li>
          </ul>
        </div>
        <div v-if="results?.users.length" class="mb-2 last:mb-0">
          <h5 class="bg-sky-600 p-3 font-bold text-sm uppercase text-white">Tác giả</h5>
          <ul>
            <li
              v-for="user in results.users"
              :key="user.id"
              class="p-2 border-b last:border-none border-dashed border-gray-200 flex items-center hover:bg-gray-200"
            >
              <div class="mr-4">
                <img
                  :src="getURlAvatar(user)"
                  alt="avatar"
                  class="w-[36px] h-[36px] rounded-full object-cover"
                />
              </div>
              <div>
                <h6 class="font-bold text-sm">{{ user.name }}</h6>
                <div class="flex gap-x-2">
                  <span class="text-xs text-gray-400">
                    Người theo dõi
                    <span class="text-gray-500 font-bold">{{
                      user.followings_count
                    }}</span>
                  </span>
                  <span class="text-xs text-gray-400">
                    Số bài viết
                    <span class="text-gray-500 font-bold">{{ user.articles_count }}</span>
                  </span>
                </div>
              </div>
            </li>
          </ul>
        </div>
        <div v-if="results?.tags.length" class="mb-2 last:mb-0">
          <h5 class="bg-sky-600 p-3 font-bold text-sm uppercase text-white">Thẻ</h5>
          <ul>
            <li
              v-for="tag in results.tags"
              :key="tag.id"
              class="p-2 border-b last:border-none border-dashed border-gray-200 hover:bg-gray-200 flex items-center"
            >
              <div>
                <h6 class="font-bold text-sm">{{ tag.name }}</h6>
                <div class="flex gap-x-2">
                  <span class="text-xs text-gray-400">
                    Người theo dõi
                    <span class="text-gray-500 font-bold">{{ tag.followers_count }}</span>
                  </span>
                  <span class="text-xs text-gray-400">
                    Số bài viết
                    <span class="text-gray-500 font-bold">{{ tag.articles_count }}</span>
                  </span>
                </div>
              </div>
            </li>
          </ul>
        </div>
      </div>
      <p v-else class="text-blue-500 p-1 text-center">Không có kết quả</p>
    </div>
  </div>
</template>

<script setup>
import apiClient from "@/api";
import { debounce, getURlAvatar } from "@/helper";
import { computed, inject, ref, watch } from "vue";
const searchKeyword = ref("");
const isFetching = ref(false);
const isOpenDropdown = ref(false);
const api = apiClient();
const results = ref();
const md = inject("md");

const search = debounce(async (keyword) => {
  if (!keyword) {
    return;
  }

  isFetching.value = true;
  try {
    const { data } = await api.search.quickSearch(keyword);
    results.value = data.results;
    isOpenDropdown.value = true;
  } catch (error) {
    console.log(error);
  }
  isFetching.value = false;
}, 1000);

watch(searchKeyword, (newValue) => {
  if (!newValue) {
    isOpenDropdown.value = false;
  }

  search(newValue);
});

const clearSearch = () => {
  searchKeyword.value = "";
};

const hasResults = computed(() => {
  return (
    results.value?.articles?.length ||
    results.value?.users?.length ||
    results.value?.tags?.length
  );
});

const printDate = (timestamp) => {
  const date = new Date(timestamp);
  return Intl.DateTimeFormat("vi-VN", {
    dateStyle: "medium",
    timeStyle: "short",
    hour12: true,
  }).format(date);
};
</script>

<style lang="scss" scoped></style>
