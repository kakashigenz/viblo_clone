<template>
  <ul v-if="!props.isFetching">
    <li v-for="article in props.data" :key="article.id">
      <div class="flex gap-x-4 border-b border-gray-300 p-2">
        <a href="#" class="">
          <img
            :src="getURlAvatar(article.user)"
            alt=""
            class="block w-[37px] h-[37px] rounded-full object-cover"
          />
        </a>
        <div class="flex-1">
          <div class="flex items-center gap-x-2 mb-1">
            <a href="#" class="text-xs text-sky-500"> {{ article.user.name }} </a>
            <span class="text-xs text-gray-400">{{
              getFormatedTime(article.created_at)
            }}</span>
          </div>
          <div class="flex items-center gap-x-2 mb-2">
            <h4>
              <a href="#" class="text-lg hover:text-sky-500 transition-all">
                {{ article.title }}
              </a>
            </h4>
            <a href="#" class="flex gap-x-1 flex-wrap">
              <Chip
                v-for="tag in article.tags"
                :key="tag.id"
                :label="tag.name"
                class="text-xs"
                :dt="{
                  background: '{gray.200}',
                }"
              />
            </a>
          </div>
          <p class="mb-2 text-sm" v-if="watchMode == WATCH_DETAIL">
            {{ article.content }}
          </p>
          <div class="flex justify-between mb-2">
            <div class="flex gap-x-2">
              <span class="text-sm">
                <i class="pi pi-bookmark" style="font-size: 12px"></i>
                {{ article.bookmarks_count }}
              </span>
              <span class="text-sm">
                <i class="pi pi-comments" style="font-size: 12px"></i>
                {{ article.comments_count }}
              </span>
            </div>
            <div>
              <span class="flex items-center gap-x-1 text-sm">
                <div class="flex flex-col">
                  <i class="pi pi-sort-up-fill" style="font-size: 8px"></i>
                  <i class="pi pi-sort-down-fill" style="font-size: 8px"></i>
                </div>
                {{ article.votes_count }}
              </span>
            </div>
          </div>
        </div>
      </div>
    </li>
  </ul>
  <ul v-else>
    <li v-for="i in 20">
      <div class="flex gap-x-4 border-b border-gray-300 p-2">
        <Skeleton shape="circle" size="2.4rem" class="mr-2"></Skeleton>
        <div class="flex-1">
          <Skeleton width="40%" height="1rem" class="mb-2"></Skeleton>
          <Skeleton width="80%" height="1rem" class="mb-2"></Skeleton>
          <Skeleton width="100%" height="1rem" class="mb-2"></Skeleton>
        </div>
      </div>
    </li>
  </ul>
</template>

<script setup>
import { getFormatedTime, getURlAvatar } from "@/helper";
import { WATCH_DETAIL } from "@/helper/constant";
import { Chip, Skeleton } from "primevue";

const props = defineProps({
  data: {
    type: Array,
    required: true,
  },
  isFetching: {
    type: Boolean,
  },
  watchMode: {
    type: Number,
    default: WATCH_DETAIL,
  },
});
</script>

<style lang="scss" scoped></style>
