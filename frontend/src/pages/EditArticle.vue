<template>
  <div class="bg-gray-200 min-h-screen flex flex-col">
    <Header />
    <div class="p-3 pb-0 flex flex-col flex-1">
      <InputText
        type="text"
        v-model="article.title"
        placeholder="Tiêu đề"
        fluid
        class="mb-6"
      />

      <div class="flex items-center mb-10 gap-x-3">
        <AutoComplete
          v-model="article.tags"
          multiple
          :suggestions="suggestions"
          placeholder="Gắn thẻ bài viết của bạn. Tối đa 5 thẻ. Ít nhất 1 thẻ !"
          @complete="search"
          class="flex-1"
          :empty-search-message="searchMessage"
        />

        <Button label="" variant="secondary" @click="toggleSaveMenu">
          Xuất bản bài viết
          <i class="pi pi-chevron-down"></i>
        </Button>
      </div>
      <div class="flex-1">
        <MDEditor
          class="h-full"
          ref="editor"
          :place-holder="placeholder"
          v-model="article.content"
        />
      </div>
    </div>

    <Popover ref="saveMenu">
      <p v-if="!isValid" class="text-sm text-gray-500 text-justify w-[320px]">
        Thêm tiêu đề, chọn ít nhất một thẻ và bắt đầu viết một cái gì đó để xuất bản.
      </p>
      <div v-else class="flex flex-col w-[320px]">
        <span class="mb-4">Xuất bản bài viết của bạn</span>
        <span class="text-sm text-gray-600">Hiển thị:</span>
        <ul>
          <li v-for="(status, index) in articleStatusTexts" :key="index" class="mb-2">
            <div v-if="index != 0" class="flex items-center gap-2">
              <RadioButton
                v-model="article.status"
                :inputId="`status ${index}`"
                :value="index"
              />
              <label :for="`status ${index}`" class="text-sm text-gray-600">{{
                status
              }}</label>
            </div>
          </li>
        </ul>
        <hr />
        <p class="mt-3 text-sm">
          <i
            class="pi mr-2"
            style="font-size: 0.8rem"
            :class="articleStatusDescs[article.status].icon"
          ></i>
          {{ articleStatusDescs[article.status].text }}
        </p>
        <div class="mt-3">
          <Button size="small" variant="secondary" @click="handleSubmit"> Lưu </Button>
        </div>
      </div>
    </Popover>
  </div>
</template>

<script setup>
import Header from "@/components/Header.vue";
import AutoComplete from "primevue/autocomplete";
import InputText from "primevue/inputtext";
import { computed, onMounted, ref } from "vue";
import { Popover, RadioButton } from "primevue";
import Button from "@/components/Button.vue";
import MDEditor from "@/components/MDEditor.vue";
import apiClient from "@/api";
import { useRoute, useRouter } from "vue-router";
import {
  CREATE_ARTICLE_ROUTE_NAME,
  DETAIL_ARTICLE_ROUTE_NAME,
  EDIT_ARTICLE_ROUTE_NAME,
} from "@/helper/constant";

const STATUS = Object.freeze({
  hidden: 0,
  draft: 1,
  visible: 2,
});

const article = ref({
  title: "",
  tags: [],
  content: "",
  status: STATUS.draft,
});
const articleStatusTexts = ["Ẩn", "Chỉ mình tôi", "Công khai"];
const articleStatusDescs = [
  {
    icon: "",
    text: "Ẩn",
  },
  {
    icon: "pi-lock",
    text: "Chỉ có bạn mới có thể xem bài viết này.",
  },
  {
    icon: "pi-globe",
    text: "Mọi người có thể thấy bài viết.",
  },
];
const data = ["C++", "C#", "Python", "PHP"];
const placeholder =
  'Cú pháp Markdown được hỗ trợ. Nhấp vào ？ để xem hướng dẫn\n\
Để xuống dòng, sử dụng thẻ <br> hoặc nhấn Enter hai lần\n\
Nhấp vào 👁 hoặc nhấn "Ctrl + P" bật/tắt chế độ xem trước\n\
Nhấp vào ▯▯ hoặc nhấn "F9" để bật/tắt chế độ xem trước song song với soạn thảo\n\
Nhấp vào 🕂 hoặc nhấn "F11" để  bật/tắt chế độ toàn màn hình\n\
Để highlight các đoạn code, hãy viết thêm tên viết thường của ngôn ngữ sau ba dấu gạch ngược (ví dụ: ```ruby)';

const editor = ref();
const saveMenu = ref();
const suggestions = ref();
const isValid = ref(true);
const searchMessage = computed(() => {
  return article.value.tags.length >= 5
    ? "Số lượng thẻ đã đạt tối đa"
    : "Không có kết quả";
});
const api = apiClient();
const route = useRoute();
const router = useRouter();

const search = (event) => {
  if (article.value.tags.length >= 5) {
    suggestions.value = [];
    return;
  }
  const set = new Set();
  const query = event.query;

  suggestions.value = data.filter((item) => {
    set.add(item.toLowerCase().trim());
    return item.toLowerCase().startsWith(query.toLowerCase());
  });

  if (!set.has(query.toLowerCase().trim())) {
    suggestions.value.push(query);
  }
};

const toggleSaveMenu = (event) => {
  if (!article.value.content || !article.value.title || !article.value.tags.length) {
    isValid.value = false;
  } else {
    isValid.value = true;
  }

  saveMenu.value.toggle(event);
};

onMounted(async () => {
  if (route.name == EDIT_ARTICLE_ROUTE_NAME) {
    const { data } = await api.article.getObject(route.params.slug);
    data.tags = data.tags.map((item) => item.name);
    article.value = data;
  }
});

const handleSubmit = async (e) => {
  try {
    let res = undefined;
    if (route.name == CREATE_ARTICLE_ROUTE_NAME) {
      res = await api.article.create(article.value);
    } else if (route.name == EDIT_ARTICLE_ROUTE_NAME) {
      res = await api.article.update(route.params.slug, article.value);
    }
    if (res.data) {
      router.push({ name: DETAIL_ARTICLE_ROUTE_NAME, params: { slug: res.data.slug } });
    }
  } catch (error) {
    console.log(error);
  }
};
</script>

<style lang="css" scoped></style>
