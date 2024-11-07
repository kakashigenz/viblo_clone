<template>
  <textarea ref="editor"></textarea>
</template>

<script setup>
import EasyMDE from "easymde";
import { nextTick, onMounted, ref } from "vue";

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
  "upload-image",
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

onMounted(() => {
  MDE = new EasyMDE({
    element: editor.value,
    placeholder: props.placeHolder,
    toolbar: props.toolbar ?? toolbars,
    spellChecker: false,
  });

  MDE.value(props.modelValue);

  MDE.codemirror.on("change", () => {
    emit("update:modelValue", MDE.value());
  });
});
</script>

<style lang="css" scoped></style>
