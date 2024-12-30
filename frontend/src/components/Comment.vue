<template>
  <div>
    <div class="flex flex-col gap-y-2 border-gray-300 rounded-sm p-6">
      <div class="flex">
        <div class="mr-3">
          <img
            :src="getURLAvatar(props.data.user)"
            alt=""
            class="w-[37px] h-[37px] rounded-full object-cover"
          />
        </div>
        <div>
          <div class="flex items-center gap-x-1">
            <a href="#" class="text-sm text-sky-500 hover:underline hover:text-sky-600">
              {{ props.data.user.name }}
            </a>
            <span class="text-sm text-gray-400">{{
              `@${props.data.user.user_name}`
            }}</span>
          </div>
          <div class="flex gap-x-3">
            <p class="text-sm text-gray-400 mb-1">
              {{ getFormatedTime(props.data.created_at) }}
            </p>
          </div>
        </div>
      </div>
      <div v-if="!isEditing" class="preview-comment" v-html="mdContent"></div>
      <div class="flex gap-x-2 items-center">
        <div class="flex gap-x-1 items-center">
          <button @click="upvote" class="text-gray-400">
            <i
              class="pi pi-chevron-up"
              :class="props.data.vote_type == UPVOTE_TYPE ? 'text-sky-600' : ''"
              style="font-size: 12px"
            ></i>
          </button>
          <span class="text-[14px] text-gray-400">{{ props.data.point }}</span>
          <button @click="downvote" class="text-gray-400">
            <i
              class="pi pi-chevron-down"
              :class="props.data.vote_type == DOWNVOTE_TYPE ? 'text-sky-600' : ''"
              style="font-size: 12px"
            ></i>
          </button>
        </div>
        <span class="text-gray-400">|</span>
        <div class="flex gap-x-3 items-center">
          <button class="text-sm text-sky-500" @click="replyComment">Trả lời</button>
          <button @click="toggleMenu" v-if="props.data.user.id == userStore.user?.id">
            <i class="pi pi-ellipsis-h text-gray-500" style="font-size: 14px"></i>
          </button>
          <Menu ref="actionMenu" :model="menuItems" :popup="true" />
        </div>
      </div>
      <PostComment
        v-if="userStore.isAuthenticated && isShowEditor"
        :parent-comment-id="props.parrentId || props.data.id"
        :comment-id="commentId"
        class="border-0"
        :value="placeholderEditor"
        cancel
        @success="addReply"
        @cancel="cancelEdit"
      />
      <ul class="pl-2">
        <li v-for="(reply, index) in replies" :key="reply.id">
          <Comment
            :data="reply"
            :parrent-id="props.data.id"
            :index="index"
            class="border-t"
            @deleted="handleDeletedReply"
          />
        </li>
      </ul>
      <div
        v-if="props.data.sub_comments_count > 0 && hasMoreReplies"
        class="flex justify-center"
      >
        <button class="text-sky-600" @click="getReplies">Xem phản hồi</button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { getFormatedTime, getURLAvatar } from "@/helper";
import { inject, ref, watch } from "vue";
import PostComment from "./PostComment.vue";
import { useUserStore } from "@/stores/user";
import apiClient from "@/api";
import {
  CREATED_STATUS,
  DOWNVOTE_TYPE,
  UPDATED_STATUS,
  UPVOTE_TYPE,
} from "@/helper/constant";
import { Menu, useToast, useConfirm } from "primevue";

const props = defineProps({
  data: {
    type: Object,
    required: true,
  },
  parrentId: {
    type: Number,
  },
  index: {
    type: Number,
  },
});
const emit = defineEmits(["deleted"]);
const userStore = useUserStore();
const md = inject("md");
const isShowEditor = ref(false);
const replies = ref([]);
const api = apiClient();
const hasMoreReplies = ref(true);
const page = ref(1);
const actionMenu = ref();
const isEditing = ref(false);
const placeholderEditor = ref("");
const mdContent = ref("");
const menuItems = ref([
  {
    label: "Sửa",
    icon: "pi pi-pencil",
    command() {
      const handledContent = props.data.content.replace(/\[@(\w+)\]\(.+\)/g, "@$1");
      commentId.value = props.data.id;
      placeholderEditor.value = handledContent;
      isShowEditor.value = true;
      isEditing.value = true;
    },
  },
  {
    label: "Xóa",
    icon: "pi pi-trash",
    command() {
      confirm.require({
        message: "Bình luận này sẽ bị xóa vĩnh viễn. Bạn có chắc không?",
        header: "Xoá bình luận",
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
          const { data, status } = await api.comment.delete(props.data.id);
          if (status == 200) {
            toast.add({
              severity: "success",
              summary: "Thông báo",
              detail: "Xóa thành công",
              life: 3000,
            });
            emit("deleted", props.index);
          }
        },
      });
    },
  },
]);
const toast = useToast();
const confirm = useConfirm();
const commentId = ref();

const toMarkdown = (content) => {
  return md.parse(content);
};

watch(
  () => props.data.sub_comments_count,
  async (newCount, prevCount) => {
    if (newCount > prevCount) {
      if (!hasMoreReplies.value) {
        page.value--;
      }
      await getReplies();
    }
  }
);

watch(
  () => props.data.content,
  (newContent) => {
    mdContent.value = toMarkdown(newContent);
  },
  { immediate: true }
);

const addReply = (reply, status) => {
  switch (status) {
    case CREATED_STATUS:
      replies.value.unshift(reply);
      isShowEditor.value = false;
      break;
    case UPDATED_STATUS:
      props.data.content = reply.content;
      isEditing.value = false;

      isShowEditor.value = false;
      break;
    default:
      break;
  }
};

const getReplies = async () => {
  try {
    const { data } = await api.comment.getReplies(props.data.id, page.value);
    replies.value = [...replies.value, ...data.data];
    hasMoreReplies.value = data.hasNext;
    page.value++;
  } catch (error) {
    console.log(error);
  }
};

const upvote = async () => {
  try {
    if (!userStore.isAuthenticated) {
      toast.add({
        severity: "error",
        summary: "Vui lòng đăng nhập để thực hiện",
        life: 3000,
      });
      return;
    }
    const { data } = await api.comment.upvote(props.data.id);
    props.data.vote_type = data.vote_type;
    props.data.point = data.value;
  } catch (error) {
    console.log(error);
  }
};

const downvote = async () => {
  try {
    if (!userStore.isAuthenticated) {
      toast.add({
        severity: "error",
        summary: "Vui lòng đăng nhập để thực hiện",
        life: 3000,
      });
      return;
    }
    const { data } = await api.comment.downvote(props.data.id);
    props.data.vote_type = data.vote_type;
    props.data.point = data.value;
  } catch (error) {
    console.log(error);
  }
};

const toggleMenu = (event) => {
  actionMenu.value.toggle(event);
};

const replyComment = (event) => {
  if (!userStore.isAuthenticated) {
    toast.add({
      severity: "error",
      summary: "Vui lòng đăng nhập để thực hiện",
      life: 3000,
    });
    return;
  }
  commentId.value = null;
  placeholderEditor.value = `@${props.data.user.user_name} `;
  isShowEditor.value = true;
};

const cancelEdit = () => {
  isShowEditor.value = false;
  isEditing.value = false;
};

const handleDeletedReply = (index) => {
  replies.value.splice(index, 1);
};
</script>

<style lang="scss" scoped></style>
