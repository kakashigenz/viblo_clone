<template>
  <div class="px-6">
    <h5 class="text-xl">Đổi mật khẩu</h5>
    <p class="text-sm mt-3">
      Thay đổi mật khẩu cho tài khoản của bạn. Bạn nên đặt mật khẩu mạnh để chặn những
      truy cập trái phép vào tài khoản của mình.
    </p>
    <form class="mt-5" @submit="onSubmit">
      <div class="flex flex-col gap-y-2 mb-4">
        <label for="password"
          ><span class="text-red-500">*</span> Mật khẩu hiện tại</label
        >
        <Password
          :feedback="false"
          toggle-mask
          id="password"
          v-model="passwordValue"
          size="small"
          fluid
        />
        <Message v-if="passwordError" severity="error" variant="simple">{{
          passwordError
        }}</Message>
      </div>
      <div class="flex flex-col gap-y-2 mb-4">
        <label for="new_password"><span class="text-red-500">*</span> Mật khẩu mới</label>
        <Password
          id="new_password"
          size="small"
          toggle-mask
          fluid
          promptLabel="Nhập mật khẩu"
          weak-label="Yếu"
          medium-label="Trung bình"
          strong-label="Mạnh"
          v-model="newPasswordValue"
        />
        <Message v-if="newPasswordError" severity="error" variant="simple">{{
          newPasswordError
        }}</Message>
      </div>
      <div class="flex flex-col gap-y-2 mb-4">
        <label for="confirmation_password"
          ><span class="text-red-500">*</span> Nhập lại mật khẩu mới</label
        >
        <Password
          id="confirmation_password"
          size="small"
          fluid
          toggle-mask
          :feedback="false"
          v-model="confirmationValue"
        />
        <Message v-if="confirmationError" severity="error" variant="simple">{{
          confirmationError
        }}</Message>
      </div>
      <div class="flex justify-end">
        <Button variant="primary">Cập nhật</Button>
      </div>
    </form>
  </div>
</template>

<script setup>
import apiClient from "@/api";
import Button from "@/components/Button.vue";
import { Message, Password, useToast } from "primevue";
import { useField, useForm } from "vee-validate";
import * as yup from "yup";

const api = apiClient();
const toast = useToast();

const schema = yup.object({
  password: yup.string().required("Thông tin này là bắt buộc"),
  new_password: yup.string().min(8, "Mật khẩu phải có tối thiểu ${min} ký tự"),
  new_password_confirmation: yup
    .string()
    .oneOf([yup.ref("new_password")], "Mật khẩu không khớp"),
});

const { handleSubmit, resetForm } = useForm({
  validationSchema: schema,
  initialValues: {
    password: "",
    new_password: "",
    new_password_confirmation: "",
  },
});
const { value: passwordValue, errorMessage: passwordError } = useField("password");
const { value: newPasswordValue, errorMessage: newPasswordError } =
  useField("new_password");
const { value: confirmationValue, errorMessage: confirmationError } = useField(
  "new_password_confirmation"
);

const onSubmit = handleSubmit(async (value) => {
  try {
    const { data } = await api.user.changePassword(value);
    if (data.message == "success") {
      toast.add({
        severity: "success",
        summary: "Thông báo",
        detail: "Đổỉ mật khẩu thành công",
        life: 2000,
      });
      resetForm();
    }
  } catch (error) {
    if (error.status == 400) {
      toast.add({
        severity: "error",
        summary: "Thông báo",
        detail: error.response.data.message,
        life: 3000,
      });
    } else {
      console.log(error);
    }
  }
});
</script>

<style lang="scss" scoped></style>
