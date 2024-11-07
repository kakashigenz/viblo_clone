<template>
  <div class="flex justify-center items-center h-screen">
    <div
      class="rounded-sm w-[445px] p-4"
      style="box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2)"
    >
      <div class="flex flex-col gap-y-6 justify-center items-center mt-[32px]">
        <div class="w-[120px]">
          <img
            src="http://images.viblo.test/images/logo_full.svg"
            alt=""
            class="w-full"
          />
        </div>
        <h4 class="font-medium text-lg mb-2">Đăng nhập vào Viblo</h4>
      </div>
      <form @submit="onSubmit" class="p-2 flex flex-col gap-y-4">
        <div class="flex flex-col gap-y-2">
          <InputGroup class="">
            <InputGroupAddon>
              <i class="pi pi-user"></i>
            </InputGroupAddon>
            <InputText
              v-model="userNameValue"
              placeholder="Tên người dùng hoặc email"
              :invalid="!!userNameMessage"
            />
          </InputGroup>
          <Message
            v-if="userNameMessage"
            severity="error"
            variant="simple"
            size="small"
            >{{ userNameMessage }}</Message
          >
        </div>
        <div class="flex flex-col gap-y-2">
          <InputGroup>
            <InputGroupAddon>
              <i class="pi pi-lock"></i>
            </InputGroupAddon>
            <InputText
              v-model="passwordValue"
              :invalid="!!passwordMessage"
              placeholder="Mật khẩu"
              type="password"
            />
          </InputGroup>
          <Message severity="error" variant="simple" size="small">{{
            passwordMessage
          }}</Message>
        </div>
        <Button>Đăng nhập</Button>
      </form>
      <div class="flex justify-between p-2">
        <a href="#" class="text-sm text-blue-500">Quên mật khẩu?</a>
        <a href="#" class="text-sm text-blue-500">Tạo tài khoản</a>
      </div>
    </div>
  </div>
</template>

<script setup>
import InputGroup from "primevue/inputgroup";
import InputGroupAddon from "primevue/inputgroupaddon";
import InputText from "primevue/inputtext";
import Button from "@/components/Button.vue";
import Message from "primevue/message";
import { useField, useForm } from "vee-validate";
import * as yup from "yup";

const schema = yup.object({
  user_name: yup.string().required("Tên người dùng/email là bắt buộc"),
  password: yup.string().required("Mật khẩu là bắt buộc"),
});
const { errors, handleSubmit } = useForm({
  validationSchema: schema,
});

const { value: userNameValue, errorMessage: userNameMessage } = useField("user_name");
const { value: passwordValue, errorMessage: passwordMessage } = useField("password");

const onSubmit = handleSubmit((value) => {
  console.log(value);
});
</script>

<style lang="scss" scoped></style>
