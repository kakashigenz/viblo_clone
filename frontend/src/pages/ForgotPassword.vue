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
        <h3 class="font-regular text-xl mb-2">Quên mật khẩu</h3>
        <p>
          Bạn quên mật khẩu của mình? Đừng lo lắng! Hãy cung cấp cho chúng tôi email bạn
          sử dụng để đăng ký tài khoản Viblo. Chúng tôi sẽ gửi cho bạn một liên kết để đặt
          lại mật khẩu của bạn qua email đó.
        </p>
      </div>
      <form v-if="!isRegistered" @submit="onSubmit" class="p-2 flex flex-col gap-y-4">
        <Message
          v-if="errorResponseMessage"
          severity="error"
          icon="pi pi-times-circle"
          class="mb-2"
          >{{ errorResponseMessage }}</Message
        >
        <div class="flex flex-col gap-y-2">
          <InputText
            v-model="emailValue"
            :invalid="!!emailMessage"
            placeholder="Địa chỉ email của bạn"
            type="text"
          />
          <Message v-if="emailMessage" severity="error" variant="simple" size="small">{{
            emailMessage
          }}</Message>
        </div>

        <Button :is-disable="loading">Đăng ký</Button>
      </form>
      <div v-else>
        <Message severity="success" class="p-3"
          >Chúng tôi đã gửi email quên mât khẩu đên địa chỉ email của bạn. Vui lòng kiểm
          tra hộp thư đến của bạn để hoàn thành. Nếu bạn không nhận được email kích hoạt
          từ chúng tôi, vui lòng ấn
          <button class="text-blue-500" @click="resendEmail">Gửi lại</button> email kích
          hoạt.</Message
        >
      </div>
    </div>
  </div>
</template>

<script setup>
import InputText from "primevue/inputtext";
import Button from "@/components/Button.vue";
import Message from "primevue/message";
import { useField, useForm } from "vee-validate";
import * as yup from "yup";
import apiClient from "@/api";
import { ref } from "vue";
import { LOGIN_ROUTE_NAME } from "@/helper/constant";
import { useToast } from "primevue/usetoast";

const schema = yup.object({
  email: yup.string().required("Email là bắt buộc").email("Email không hợp lệ"),
});
const { handleSubmit } = useForm({
  validationSchema: schema,
});

const { value: emailValue, errorMessage: emailMessage } = useField("email");

const api = apiClient();
const loading = ref(false);
const errorResponseMessage = ref("");
const isRegistered = ref(false);
const toast = useToast();

const onSubmit = handleSubmit(async (value) => {
  if (loading.value) {
    return;
  }

  loading.value = true;
  try {
    const { data } = await api.auth.forgotPassword(value);
    if (data.status == "success") {
      isRegistered.value = true;
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

const resendEmail = async () => {
  console.log("object");
  try {
    const payload = {
      email: emailValue.value,
    };
    const { data } = await api.auth.forgotPassword(payload);
    if (data.status == "success") {
      toast.add({
        severity: "success",
        summary: "Thông báo",
        detail: data.data.message,
        life: 2000,
      });
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
};
</script>

<style lang="scss" scoped></style>
