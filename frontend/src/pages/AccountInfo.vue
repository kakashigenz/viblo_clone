<template>
  <div class="px-6">
    <h5 class="text-xl">Thông tin cá nhân</h5>
    <p class="text-sm mt-3">Quản lý thông tin của bạn</p>
    <div class="flex items-center justify-center mt-5">
      <label for="upload_input" class="relative cursor-pointer">
        <img
          :src="getURLAvatar(userStore.user)"
          class="w-[128px] h-[128px] object-cover rounded-full"
          alt="avatar"
        />
        <p class="absolute bottom-0 bg-black bg-opacity-60 text-white w-full text-center">
          Thay đổi
        </p>
      </label>
      <input
        type="file"
        id="upload_input"
        @change="handleUpload"
        class="hidden"
        accept="image/*"
      />
    </div>
    <form class="mt-5" @submit="onSubmit">
      <div class="flex flex-col gap-y-2 mb-4">
        <label for="user_name">Tên tài khoản</label>
        <InputText
          disabled
          :default-value="userStore.user.user_name"
          id="user_name"
          size="small"
        />
      </div>
      <div class="flex flex-col gap-y-2 mb-4">
        <label><span class="text-red-500">*</span> Tên hiển thị</label>
        <InputText size="small" v-model="nameValue" />
        <Message v-if="nameError" severity="error" variant="simple" size="small">{{
          nameError
        }}</Message>
      </div>
      <div class="flex gap-x-3">
        <div class="flex flex-col gap-y-2 mb-4 w-1/2">
          <label><span class="text-red-500">*</span> Ngày sinh</label>
          <DatePicker
            show-button-bar
            date-format="dd/mm/yy"
            size="small"
            v-model="birthdayValue"
          />
          <Message v-if="birthdayError" severity="error" variant="simple" size="small">{{
            birthdayError
          }}</Message>
        </div>
        <div class="flex flex-col gap-y-2 mb-4 w-1/2">
          <label><span class="text-red-500">*</span> Giới tính</label>
          <Select
            :options="genderOption"
            option-label="name"
            option-value="value"
            size="small"
            v-model="genderValue"
          />
          <Message v-if="genderError" severity="error" variant="simple" size="small">{{
            genderError
          }}</Message>
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
import { DatePicker, InputText, Message, Select, useToast } from "primevue";
import { useField, useForm } from "vee-validate";
import * as yup from "yup";
import { ref } from "vue";
import { FEMALE_GENDER, MALE_GENDER, OTHER_GENDER } from "@/helper/constant";
import apiClient from "@/api";
import { useUserStore } from "@/stores/user";
import { getURLAvatar } from "@/helper";

const api = apiClient();
const toast = useToast();
const userStore = useUserStore();
const handleUpload = async (event) => {
  const file = event.target.files[0];
  try {
    const { data: firstData } = await api.images.createPresignedURL({ name: file.name });
    const { status } = await api.images.uploadImage(file, firstData.url);
    if (status == 200) {
      const { data } = await api.user.updateAvatar({ name: firstData.name });
      if (data.new_avatar) {
        toast.add({
          severity: "success",
          summary: "Thông báo",
          detail: "Thay đổi thành công",
          life: 2000,
        });
        userStore.user.avatar = data.new_avatar;
      }
    }
  } catch (error) {
    console.log(error);
  }
};

const genderOption = ref([
  { name: "Nam", value: 1 },
  { name: "Nữ", value: 0 },
  { name: "Khác", value: 2 },
]);

const schema = yup.object({
  name: yup.string().required("Tên là bắt buộc"),
  birthday: yup
    .date()
    .required("Ngày không được để trống")
    .typeError("Giá trị không hợp lệ"),
  gender: yup
    .number()
    .required("Giới tính không được để trống")
    .oneOf([MALE_GENDER, FEMALE_GENDER, OTHER_GENDER], "Giá trị không hợp lệ"),
});

const { handleSubmit } = useForm({
  validationSchema: schema,
  initialValues: {
    name: userStore.user.name,
    birthday: userStore.user.birthday,
    gender: userStore.user.gender,
  },
});
const { value: nameValue, errorMessage: nameError } = useField("name");
const { value: birthdayValue, errorMessage: birthdayError } = useField("birthday");
const { value: genderValue, errorMessage: genderError } = useField("gender");

const onSubmit = handleSubmit(async (value) => {
  try {
    const { data } = await api.user.update(value);
    if (data.message == "success") {
      toast.add({
        severity: "success",
        summary: "Thông báo",
        detail: "Thành công",
        life: 2000,
      });
      const { data: userData } = await api.auth.isAuthorized();
      userStore.user = userData;
    }
  } catch (error) {
    console.log(error);
  }
});
</script>

<style lang="scss" scoped></style>
