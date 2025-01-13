<template>
  <div class="flex flex-col items-center h-screen">
    <div class="w-[120px] my-6">
      <RouterLink :to="{ name: LOGIN_ROUTE_NAME }">
        <img src="/images/logo_full.svg" alt="" class="w-full" />
      </RouterLink>
    </div>
    <div
      class="rounded-sm w-[560px] p-4"
      style="box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2)"
    >
      <div class="flex flex-col gap-y-4 p-2">
        <h3 class="font-regular text-xl mb-2">Thay đổi mật khẩu</h3>
        <p>Hãy thay đổi mật khẩu mới cho tài khoản của bạn.</p>
      </div>
      <form v-if="!isSubmited" @submit="onSubmit" class="p-2 flex flex-col gap-y-4">
        <Message
          v-if="errorResponseMessage"
          severity="error"
          icon="pi pi-times-circle"
          class="mb-2"
          >{{ errorResponseMessage }}</Message
        >

        <div class="flex flex-col gap-y-2">
          <Password
            v-model="passwordValue"
            :invalid="!!passwordMessage"
            placeholder="Mật khẩu"
            toggle-mask
            fluid
            prompt-label="Nhập mật khẩu"
            weak-label="Mật khẩu yếu"
            medium-label="Mật khẩu trung bình"
            strong-label="Mật khẩu mạnh"
          />
          <Message
            v-if="passwordMessage"
            severity="error"
            variant="simple"
            size="small"
            >{{ passwordMessage }}</Message
          >
        </div>

        <div class="flex flex-col gap-y-2">
          <Password
            v-model="passwordConfirmationValue"
            :invalid="!!passwordConfirmationMessage"
            placeholder="Xác nhận mật khẩu của bạn"
            toggle-mask
            :feedback="false"
            fluid
          />
          <Message
            v-if="passwordConfirmationMessage"
            severity="error"
            variant="simple"
            size="small"
            >{{ passwordConfirmationMessage }}</Message
          >
        </div>
        <Button :is-disable="loading">Đăng ký</Button>
      </form>
      <div v-else>
        <Message severity="success" class="p-3"
          >Thay đổi mật khẩu thành công. Hãy
          <RouterLink :to="{ name: LOGIN_ROUTE_NAME }" class="text-blue-500"
            >Đăng nhập</RouterLink
          >
          với mật khẩu mới của bạn.</Message
        >
      </div>
    </div>
  </div>
</template>

<script setup>
import Button from "@/components/Button.vue";
import Message from "primevue/message";
import { useField, useForm } from "vee-validate";
import * as yup from "yup";
import apiClient from "@/api";
import { useRoute } from "vue-router";
import { ref } from "vue";
import Password from "primevue/password";
import { LOGIN_ROUTE_NAME } from "@/helper/constant";
import { useToast } from "primevue/usetoast";

const schema = yup.object({
  password: yup
    .string()
    .required("Mật khẩu là bắt buộc")
    .min(8, "Mật khẩu phải dài hơn ${min} ký tự"),
  password_confirmation: yup
    .string()
    .required("Nhập lại mật khẩu là bắt buộc")
    .oneOf([yup.ref("password")], "Mật khẩu không khớp"),
});
const { handleSubmit } = useForm({
  validationSchema: schema,
});

const { value: passwordValue, errorMessage: passwordMessage } = useField("password");
const { value: passwordConfirmationValue, errorMessage: passwordConfirmationMessage } =
  useField("password_confirmation");

const api = apiClient();
const loading = ref(false);
const isSubmited = ref(false);
const errorResponseMessage = ref("");
const route = useRoute();
const toast = useToast();

const onSubmit = handleSubmit(async (value) => {
  if (loading.value) {
    return;
  }

  loading.value = true;
  try {
    const payload = {
      ...value,
      token: route.query.token,
      email: route.query.email,
    };
    const { data } = await api.auth.resetPassword(payload);
    if (data.status == "success") {
      isSubmited.value = true;
    } else if (data.status == "fail") {
      toast.add({
        severity: "error",
        summary: "Có lỗi xảy ra",
        detail: data.data.message,
        life: 2000,
      });
    }
  } catch (error) {
    if (error.status == 422) {
      errorResponseMessage.value = error?.response?.data?.message;
    } else {
      console.log(error);
    }
  }
  loading.value = false;
});
</script>

<style lang="scss" scoped></style>
