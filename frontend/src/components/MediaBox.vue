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
      <div class="grid gap-4" :class="props.edit ? 'grid-cols-4' : 'grid-cols-6'">
        <div
          v-for="(image, index) in images"
          :key="image.id"
          :class="props.edit ? 'image-container' : ''"
        >
          <img
            @click="$emit('select', $event, image)"
            :src="image.url"
            alt=""
            class="w-full h-full block object-cover hover:cursor-pointer"
          />
          <div v-if="props.edit" class="image-option">
            <a target="_blank" :href="image.url" class="hover:text-sky-600">
              <i class="pi pi-download"></i>
            </a>
            <button @click="deleteImage(image, index)" class="hover:text-sky-600">
              <i class="pi pi-trash"></i>
            </button>
          </div>
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
import { Paginator, useToast } from "primevue";
import { inject, ref, watch } from "vue";
import Media from "./Media.vue";

defineEmits(["select"]);

const props = defineProps({
  edit: Boolean,
});
const images = ref([]);
const api = apiClient();
const paginator = ref({
  currentPage: 0,
  total: 0,
  size: 0,
});
const toast = useToast();

const fetchImages = async (page) => {
  const { data } = await api.images.getList(page);
  images.value = data.data;
  paginator.value.total = data.total;
  paginator.value.size = data.size;
};

const deleteImage = async (image, index) => {
  const { data, status } = await api.images.delete(image.name);
  if (data.message == "success" && status == 200) {
    toast.add({
      severity: "success",
      summary: "Thông báo",
      detail: "Xóa thành công",
      life: 2000,
    });
    images.value.splice(index, 1);
    paginator.value.total--;
  }
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

<style lang="css" scoped>
.image-container {
  position: relative;
  aspect-ratio: 1/1;
}

.image-container:hover .image-option {
  display: flex;
}

.image-option {
  position: absolute;
  bottom: 0;
  width: 100%;
  background-color: rgba(255, 255, 255, 0.8);
  display: none;
  justify-content: center;
  align-items: center;
  column-gap: 12px;
}
</style>
