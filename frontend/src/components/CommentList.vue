<template>
  <div>
    <PostComment @success="addComment" />
    <ul class="flex flex-col gap-y-3">
      <li v-for="(comment, index) in comments" :key="comment.id">
        <Comment
          :data="comment"
          :index="index"
          class="border"
          @deleted="handleDeletedComment"
        />
      </li>
    </ul>
    <div v-if="hasNext" class="flex justify-center mt-4">
      <button class="text-sky-600" @click="getMoreComments">Xem thêm</button>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted, ref } from "vue";
import Comment from "./Comment.vue";
import { useRoute } from "vue-router";
import apiClient from "@/api";
import PostComment from "./PostComment.vue";

const props = defineProps({
  comments: {
    type: Array,
    default: [],
  },
});

const route = useRoute();
const paginator = ref({
  page: 1,
  total: 0,
  size: 10,
});
const comments = ref([]);
const api = apiClient();

onMounted(async () => {
  const { data: commentData } = await api.comment.getList(route.params.slug);
  comments.value = commentData.data;
  paginator.value.page = commentData.page;
  paginator.value.total = commentData.total;
  paginator.value.size = commentData.size;
});

const hasNext = computed(() => {
  return paginator.value.page * paginator.value.size < paginator.value.total;
});

const getMoreComments = async () => {
  try {
    const { data: commentData } = await api.comment.getList(
      route.params.slug,
      paginator.value.page + 1
    );
    comments.value = [...comments.value, ...commentData.data];
    paginator.value.page = commentData.page;
    paginator.value.total = commentData.total;
    paginator.value.size = commentData.size;
    paginator.page++;
  } catch (error) {
    console.log(error);
  }
};

const addComment = (comment) => {
  comments.value.unshift(comment);
};

const handleDeletedComment = (index) => {
  comments.value.splice(index, 1);
};
</script>

<style lang="scss" scoped></style>
