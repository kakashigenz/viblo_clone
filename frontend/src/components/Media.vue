<template>
  <div class="w-full">
    <Toast />
    <FileUpload
      customUpload
      @uploader="handleUpload($event)"
      :multiple="true"
      accept="image/*"
    >
      <template #header="{ chooseCallback, uploadCallback, clearCallback, files }">
        <div class="flex flex-wrap justify-between items-center flex-1 gap-4">
          <div class="flex gap-2">
            <Button
              @click="chooseCallback()"
              icon="pi pi-images"
              rounded
              outlined
              severity="secondary"
            ></Button>
            <Button
              @click="uploadCallback"
              icon="pi pi-cloud-upload"
              rounded
              outlined
              severity="success"
              :disabled="!files || files.length === 0"
            ></Button>
            <Button
              @click="clearCallback()"
              icon="pi pi-times"
              rounded
              outlined
              severity="danger"
              :disabled="!files || files.length === 0"
            ></Button>
          </div>
        </div>
      </template>
      <template #empty>
        <div class="flex items-center justify-center flex-col">
          <i
            class="pi pi-cloud-upload !border-2 !rounded-full !p-8 !text-4xl !text-muted-color"
          />
          <p class="mt-6 mb-0">Kéo thả file vào đây để upload.</p>
        </div>
      </template>
    </FileUpload>
    <Loading :open="isLoading" />
  </div>
</template>

<script setup>
import Toast from "primevue/toast";
import FileUpload from "primevue/fileupload";
import { useToast } from "primevue/usetoast";
import { Button } from "primevue";
import apiClient from "@/api";
import Loading from "./Loading.vue";
import { ref } from "vue";
const api = apiClient();
const toast = useToast();
const isLoading = ref(false);

const handleUpload = async ({ files }) => {
  const promises = files.map((item) => {
    const promise = new Promise(async (res, rej) => {
      try {
        const payload = {
          name: item.name,
        };

        const { data } = await api.images.createPresignedURL(payload);
        const { status } = await api.images.uploadImage(item, data.url);
        if (status == 200) {
          const { data: responseStore } = await api.images.store(data);
          res(responseStore.message);
        }
      } catch (error) {
        rej(error);
      }
    });

    return promise;
  });

  isLoading.value = true;
  try {
    const responses = await Promise.all(promises);
    toast.add({
      severity: "success",
      summary: "Thông báo",
      detail: "Tải file thành công",
      life: 3000,
    });
  } catch (error) {
    toast.add({
      severity: "error",
      summary: "Thông báo",
      detail: "Có lỗi xảy ra vui lòng thử lại sau",
      life: 3000,
    });
    console.log(error);
  }
  isLoading.value = false;
};
</script>

<style lang="scss" scoped></style>
