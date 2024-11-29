<template>
  <div class="bg-white">
    <Media
      @uploadSuccess="
        (event) => {
          fetchImages(paginator.currentPage);
        }
      "
    />
    <div class="mt-5">
      <h4 class="mb-4">Ảnh của bạn</h4>
      <div class="grid grid-cols-6 gap-4">
        <div v-for="image in images" :key="image.id" class="">
          <img
            @click="$emit('select', $event, image)"
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
</template>

<script setup>
import apiClient from "@/api";
import { Paginator } from "primevue";
import { inject, ref, watch } from "vue";
import Media from "./Media.vue";

defineEmits(["select"]);

const images = ref([]);
const api = apiClient();
const paginator = ref({
  currentPage: 0,
  total: 0,
  size: 0,
});

const fetchImages = async (page) => {
  const { data } = await api.images.getList(page);
  images.value = data.data;
  paginator.value.total = data.total;
  paginator.value.size = data.size;
};

watch(
  () => paginator.value.currentPage,
  async (newValue) => {
    await fetchImages(newValue);
  },
  { immediate: true }
);

function changePage(event) {
  paginator.value.currentPage = event.page + 1;
}
</script>

<style lang="scss" scoped></style>
