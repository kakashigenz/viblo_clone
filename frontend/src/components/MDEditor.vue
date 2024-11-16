<template>
  <div>
    <textarea ref="editor"></textarea>
    <Dialog
      v-model:visible="visible"
      modal
      header="Thêm hình ảnh thu nhỏ"
      class="w-[720px]"
    >
      <div class="bg-white">
        <Media :handle="props.handleUpload" />
        <div class="mt-5">
          <h4 class="mb-4">Ảnh của bạn</h4>
          <div class="grid grid-cols-6 gap-4">
            <div v-for="image in images" :key="image.id" class="">
              <img
                @click="selectImage($event, image)"
                :src="image.url"
                alt=""
                class="w-full block object-cover hover:cursor-pointer"
              />
            </div>
          </div>
          <Paginator
            v-if="images.length < paginator.total"
            :rows="paginator.size"
            :totalRecords="paginator.total"
            template="PrevPageLink PageLinks NextPageLink"
            @page="changePage"
          />
        </div>
      </div>
    </Dialog>
  </div>
</template>

<script setup>
import EasyMDE from "easymde";
import { nextTick, onMounted, ref, watch } from "vue";
import Dialog from "primevue/dialog";
import Paginator from "primevue/paginator";
import Media from "./Media.vue";
import apiClient from "@/api";

const props = defineProps({
  placeHolder: {
    type: String,
    default: "",
  },
  toolbar: {
    type: Array,
  },
  modelValue: {
    type: String,
    default: "",
  },
});

const toolbars = [
  "bold",
  "italic",
  "strikethrough",
  "heading-1",
  "heading-2",
  "heading-3",
  "|",
  "code",
  "quote",
  "unordered-list",
  "ordered-list",
  "table",
  "horizontal-rule",
  "clean-block",
  "|",
  {
    name: "upload-image",
    action: openSelectBox,
    className: "fa fa-image",
    title: "Upload Image",
  },
  "link",
  "|",
  "preview",
  "side-by-side",
  "fullscreen",
  "|",
  "undo",
  "redo",
  "|",
  "guide",
];
const editor = ref();
let MDE = null;
const emit = defineEmits(["update:modelValue"]);
const visible = ref(false);
const images = ref([]);
const api = apiClient();
const paginator = ref({
  currentPage: 0,
  total: 0,
  size: 0,
});

watch(
  () => paginator.value.currentPage,
  async (newValue) => {
    const { data } = await api.images.getList(newValue);
    images.value = data.data;
    paginator.value.total = data.total;
    paginator.value.size = data.size;
  },
  { immediate: true }
);

onMounted(() => {
  MDE = new EasyMDE({
    element: editor.value,
    placeholder: props.placeHolder,
    toolbar: props.toolbar ?? toolbars,
    spellChecker: false,
    maxHeight: "calc(100vh - 340px)",
  });

  MDE.value(props.modelValue);

  MDE.codemirror.on("change", () => {
    emit("update:modelValue", MDE.value());
  });
});

function openSelectBox(e) {
  visible.value = true;
}

function changePage(event) {
  paginator.value.currentPage = event.page + 1;
}

function selectImage(event, image) {
  const imageMarkdown = `![Image](${image.url})`;
  MDE.codemirror.replaceSelection(imageMarkdown);
  visible.value = false;
}
</script>

<style lang="css" scoped></style>
