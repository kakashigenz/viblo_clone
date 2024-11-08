<template>
  <button :class="className" :disabled="props.loading">
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
  loading: {
    type: Boolean,
  },
});

const className = ref("flex items-center gap-x-3 text-black border transition-all ");

switch (props.variant) {
  case "primary":
    className.value =
      "font-regular rounded-sm bg-blue-500 text-white hover:bg-blue-400 disabled:cursor-not-allowed disabled:bg-blue-400";
    break;
  case "secondary":
    className.value +=
      "font-regular bg-white border-gray-200 rounded-sm hover:text-blue-500 hover:bg-blue-100 hover:border-blue-400 disabled:cursor-not-allowed disabled:bg-blue-400 ";
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
