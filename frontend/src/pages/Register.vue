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
        <h3 class="font-regular text-xl mb-2">Đăng ký tài khoản Viblo</h3>
        <p>
          Chào mừng bạn đến <b>Nền tảng Viblo!</b> Tham gia cùng chúng tôi để tìm kiếm
          thông tin hữu ích cần thiết để cải thiện kỹ năng IT của bạn. Vui lòng điền thông
          tin của bạn vào biểu mẫu bên dưới để tiếp tục.
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
            v-model="nameValue"
            placeholder="Tên của bạn"
            :invalid="!!nameMessage"
          />
          <Message severity="error" variant="simple" size="small">{{
            nameMessage
          }}</Message>
        </div>
        <div class="flex gap-x-4">
          <div class="flex flex-col gap-y-2 basis-1/2">
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
          <div class="flex flex-col gap-y-2 basis-1/2">
            <InputText
              v-model="userNameValue"
              :invalid="!!userNameMessage"
              placeholder="Tên tài khoản"
              type="text"
            />
            <Message
              v-if="userNameMessage"
              severity="error"
              variant="simple"
              size="small"
              >{{ userNameMessage }}</Message
            >
          </div>
        </div>

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
        <Button :loading="loading">Đăng ký</Button>
      </form>
      <div v-else>
        <Message severity="success" class="p-3"
          >Chào mừng <b class="text-green-700">{{ nameValue }}</b
          >, tài khoản của bạn đã được đăng ký thành công. Chúng tôi đã gửi cho bạn một
          email kích hoạt tại địa chỉ email <b class="text-green-700">{{ emailValue }}</b
          >. Vui lòng kiểm tra hộp thư đến của bạn để hoàn thành. Nếu bạn không nhận được
          email kích hoạt từ chúng tôi, vui lòng ấn
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
import { useRouter } from "vue-router";
import { ref } from "vue";
import Password from "primevue/password";
import { LOGIN_ROUTE_NAME } from "@/helper/constant";

const schema = yup.object({
  name: yup.string().required("Tên là bắt buộc"),
  email: yup.string().required("Email là bắt buộc").email("Email không hợp lệ"),
  user_name: yup.string().required("Tên tài khoản là bắt buộc"),
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

const { value: nameValue, errorMessage: nameMessage } = useField("name");
const { value: emailValue, errorMessage: emailMessage } = useField("email");
const { value: userNameValue, errorMessage: userNameMessage } = useField("user_name");
const { value: passwordValue, errorMessage: passwordMessage } = useField("password");
const { value: passwordConfirmationValue, errorMessage: passwordConfirmationMessage } =
  useField("password_confirmation");

const api = apiClient();
const router = useRouter();
const loading = ref(false);
const isRegistered = ref(false);
const errorResponseMessage = ref("");

const onSubmit = handleSubmit(async (value) => {
  if (loading.value) {
    return;
  }

  loading.value = true;
  try {
    const { status } = await api.auth.getCSRFToken();
    if (status == 204) {
      const { data } = await api.auth.register(value);
      if (data.message == "success") {
        isRegistered.value = true;
      }
    }
  } catch (error) {
    if (error.status == 400) {
      errorResponseMessage.value = error?.response?.data?.message;
    } else {
      console.log(error);
    }
  }
  loading.value = false;
});

const resendEmail = async (e) => {
  const { data } = await api.verificationEmail.resendEmail();
  if (data.message == "success") {
    toast.add({
      severity: "success",
      summary: "Thông báo",
      detail: "Gửi thành công",
      life: 3000,
    });
  }
};
</script>

<style lang="scss" scoped></style>
