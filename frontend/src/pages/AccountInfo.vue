<template>
  <div class="px-6">
    <h5 class="text-xl">Thông tin cá nhân</h5>
    <p class="text-sm mt-3">Quản lý thông tin của bạn</p>
    <div class="flex items-center justify-center mt-5">
      <label for="upload_input" class="relative cursor-pointer">
        <img
          src="http://images.viblo.test/images/thumb.jpg"
          class="w-[128px] h-[128px] object-cover rounded-full"
          alt="avatar"
        />
        <p class="absolute bottom-0 bg-black bg-opacity-60 text-white w-full text-center">
          Thay đổi
        </p>
      </label>
      <input type="file" id="upload_input" @change="handleUpload" class="hidden" />
    </div>
    <form class="mt-5">
      <div class="flex flex-col gap-y-2 mb-4">
        <label for="user_name">Tên tài khoản</label>
        <InputText disabled default-value="tanjiro2312" id="user_name" size="small" />
      </div>
      <div class="flex flex-col gap-y-2 mb-4">
        <label for="name"><span class="text-red-500">*</span> Tên hiển thị</label>
        <InputText id="name" size="small" />
      </div>
      <div class="flex gap-x-3">
        <div class="flex flex-col gap-y-2 mb-4 w-1/2">
          <label for="birthday"><span class="text-red-500">*</span> Ngày sinh</label>
          <DatePicker date-format="dd/mm/yy" show-button-bar id="birthday" size="small" />
        </div>
        <div class="flex flex-col gap-y-2 mb-4 w-1/2">
          <label for="gender"><span class="text-red-500">*</span> Giới tính</label>
          <Select :options="genderOption" option-label="name" id="gender" size="small" />
        </div>
      </div>
      <div class="flex justify-end">
        <Button variant="primary">Cập nhật</Button>
      </div>
    </form>
  </div>
</template>

<script setup>
import Button from "@/components/Button.vue";
import { DatePicker, InputText, Select } from "primevue";
import { ref } from "vue";
const handleUpload = async (event) => {
  const file = event.target.files[0];
  console.log(file);
};

const genderOption = ref([
  { name: "Nam", value: 1 },
  { name: "Nữ", value: 0 },
  { name: "Khác", value: 2 },
]);
</script>

<style lang="scss" scoped></style>
