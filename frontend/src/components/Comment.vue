<template>
  <div class="flex flex-col gap-y-2 border border-gray-300 rounded-sm p-6">
    <div class="flex">
      <div class="mr-3">
        <img
          src="http://images.viblo.test/images/thumb.jpg"
          alt=""
          class="w-[37px] h-[37px] rounded-full object-cover"
        />
      </div>
      <div>
        <div class="flex items-center gap-x-1">
          <a href="#" class="text-sm text-sky-500 hover:underline hover:text-sky-600">
            Nam
          </a>
          <span class="text-sm text-gray-400">{{ `@namdao` }}</span>
        </div>
        <div class="flex gap-x-3">
          <p class="text-sm text-gray-400 mb-1">
            {{ getFormatedTime("2024-11-29T14:42:30") }}
          </p>
        </div>
      </div>
    </div>
    <div class="preview-comment">
      Lorem ipsum dolor sit, amet consectetur adipisicing elit. Recusandae magni mollitia
      quos consectetur. Ex, molestiae? Voluptatum, repellendus repellat culpa aspernatur
      nulla quo molestias fugit. Quam laborum nulla numquam eos natus!
    </div>
    <div v-if="userStore.isAuthenticated" class="flex gap-x-2 items-center">
      <div class="flex gap-x-1 items-center">
        <button class="text-gray-400">
          <i class="pi pi-chevron-up" style="font-size: 12px"></i>
        </button>
        <span class="text-[14px] text-gray-400">3</span>
        <button class="text-gray-400">
          <i class="pi pi-chevron-down" style="font-size: 12px"></i>
        </button>
      </div>
      <span class="text-gray-400">|</span>
      <div class="flex gap-x-3 items-center">
        <button class="text-sm text-sky-500">Trả lời</button>
        <button>
          <i class="pi pi-ellipsis-h text-gray-500" style="font-size: 14px"></i>
        </button>
      </div>
    </div>
    <PostComment v-if="userStore.isAuthenticated" class="border-0" />
    <ul v-if="replies.length" class="pl-2">
      <li v-for="reply in replies">
        <Comment />
      </li>
    </ul>
  </div>
</template>

<script setup>
import { getFormatedTime } from "@/helper";
import { ref } from "vue";
import PostComment from "./PostComment.vue";
import { useUserStore } from "@/stores/user";

const replies = ref([]);
const userStore = useUserStore();
</script>

<style lang="scss" scoped></style>
