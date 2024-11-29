<template>
  <Tabs
    v-if="userStore.isAuthenticated"
    class="border border-gray-300 rounded-sm mb-6 px-6"
    :dt="{
      'tab.padding': '14px 8px',
      'tabpanel.padding': '12px 0',
    }"
    value="0"
  >
    <TabList>
      <Tab
        :pt="{
          root: 'text-sm',
        }"
        value="0"
        >Viết</Tab
      >
      <Tab
        :pt="{
          root: 'text-sm',
        }"
        value="1"
        >Xem trước</Tab
      >
    </TabList>
    <TabPanels>
      <TabPanel value="0">
        <div class="flex gap-x-2">
          <div class="">
            <img
              :src="getURlAvatar(userStore.user)"
              class="w-[37px] h-[37px] rounded-full object-cover"
              alt=""
            />
          </div>
          <div class="flex-1">
            <div class="relative">
              <textarea
                ref="textArea"
                class="block w-full border-gray-300 outline-none border rounded-sm px-4 py-3"
                v-model.lazy="commentValue"
                rows="4"
                style="resize: none"
              ></textarea>
              <button @click="visible = true" class="absolute top-4 right-4">
                <i class="pi pi-image"></i>
              </button>
            </div>
            <div class="flex justify-end">
              <Button :is-disable="!commentValue" variant="primary" class="py-2 px-5 mt-2"
                >Bình luận</Button
              >
            </div>
            <Dialog
              v-model:visible="visible"
              modal
              header="Thêm hình ảnh thu nhỏ"
              class="w-[720px]"
            >
              <MediaBox @select="selectImage" />
            </Dialog>
          </div>
        </div>
      </TabPanel>
      <TabPanel value="1">
        <div class="preview-comment" v-html="previewMarkdown"></div>
      </TabPanel>
    </TabPanels>
  </Tabs>
  <RouterLink
    :to="{ name: LOGIN_ROUTE_NAME }"
    v-else
    class="flex justify-center items-center border border-gray-200 rounded-lg py-6 gap-1 mb-8 text-gray-400"
  >
    <i class="pi pi-comment"></i>
    <span>Đăng nhập để bình luận</span>
  </RouterLink>
</template>

<script setup>
import Tabs from "primevue/tabs";
import TabList from "primevue/tablist";
import Tab from "primevue/tab";
import TabPanels from "primevue/tabpanels";
import TabPanel from "primevue/tabpanel";
import { useUserStore } from "@/stores/user";
import { getURlAvatar } from "@/helper";
import { LOGIN_ROUTE_NAME } from "@/helper/constant";
import { Dialog } from "primevue";
import Button from "./Button.vue";
import { computed, inject, onMounted, ref } from "vue";
import MediaBox from "./MediaBox.vue";

const userStore = useUserStore();
const commentValue = ref("");
const visible = ref(false);
const textArea = ref();
const md = inject("md");

const previewMarkdown = computed(() => {
  return md.parse(commentValue.value);
});

const selectImage = (event, image) => {
  const imageMarkdown = `![Image](${image.url})`;
  const start = textArea.value.selectionStart;
  const end = textArea.value.selectionEnd;

  commentValue.value =
    commentValue.value.slice(0, start) +
    " " +
    imageMarkdown +
    " " +
    commentValue.value.slice(end);
  visible.value = false;
};
</script>

<style lang="scss" scoped></style>
