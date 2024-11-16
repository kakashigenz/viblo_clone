<template>
  <Teleport to="body">
    <div
      v-if="props.open"
      class="bg-black bg-opacity-50 fixed inset-0 flex justify-center items-center z-[10000]"
    >
      <ProgressSpinner
        style="width: 50px; height: 50px"
        strokeWidth="5"
        fill="transparent"
        aria-label="Loading"
      />
    </div>
  </Teleport>
</template>

<script setup>
import ProgressSpinner from "primevue/progressspinner";

const props = defineProps({
  open: {
    type: Boolean,
    default: false,
  },
});
</script>

<style lang="scss" scoped></style>
