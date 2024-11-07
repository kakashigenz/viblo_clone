<template>
  <button :class="className">
    <slot />
  </button>
</template>

<script setup>
import { ref } from "vue";

const props = defineProps({
  variant: {
    type: String,
    default: "primary",
  },
  size: {
    type: String,
    default: "medium",
  },
});

const className = ref(
  "flex items-center gap-x-3 text-black border transition-all bg-white "
);
switch (props.variant) {
  case "primary":
    className.value = "font-regular rounded-sm bg-blue-500 text-white hover:bg-blue-400 ";
    break;
  case "secondary":
    className.value +=
      "font-regular border-gray-200 rounded-sm hover:text-blue-500 hover:bg-blue-100 hover:border-blue-400 ";
    break;
  default:
    break;
}

switch (props.size) {
  case "small":
    className.value += "px-3 py-1 text-sm ";
    break;
  case "medium":
    className.value += "px-[20px] py-[10px] text-sm";
    break;
  default:
    break;
}
</script>

<style lang="scss" scoped></style>
