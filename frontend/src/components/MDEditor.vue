<template>
  <div>
    <textarea ref="editor"></textarea>
    <Dialog
      v-model:visible="visible"
      modal
      header="Thêm hình ảnh thu nhỏ"
      class="w-[720px]"
    >
      <MediaBox @select="selectImage" />
    </Dialog>
  </div>
</template>

<script setup>
import EasyMDE from "easymde";
import { nextTick, onBeforeMount, onMounted, onUpdated, ref, watch } from "vue";
import Dialog from "primevue/dialog";
import Paginator from "primevue/paginator";
import Media from "./Media.vue";
import MediaBox from "./MediaBox.vue";

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
defineExpose({
  setValue,
});

function setValue(content) {
  MDE.value(content);
}

onMounted(() => {
  MDE = new EasyMDE({
    element: editor.value,
    placeholder: props.placeHolder,
    toolbar: props.toolbar ?? toolbars,
    spellChecker: false,
    maxHeight: "calc(100vh - 340px)",
  });

  MDE.codemirror.on("change", () => {
    emit("update:modelValue", MDE.value());
  });
});

function openSelectBox(e) {
  visible.value = true;
}

function selectImage(event, image) {
  const imageMarkdown = `![Image](${image.url})`;
  MDE.codemirror.replaceSelection(imageMarkdown);
  visible.value = false;
}
</script>

<style lang="css" scoped></style>
