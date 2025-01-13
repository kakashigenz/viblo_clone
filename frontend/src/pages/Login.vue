<template>
  <div class="flex justify-center items-center h-screen">
    <div
      class="rounded-sm w-[445px] p-4"
      style="box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2)"
    >
      <div class="flex flex-col gap-y-6 justify-center items-center mt-[32px]">
        <div class="w-[120px]">
          <img src="/images/logo_full.svg" alt="" class="w-full" />
        </div>
        <h4 class="font-medium text-lg mb-2">Đăng nhập vào Viblo</h4>
      </div>
      <form @submit="onSubmit" class="p-2 flex flex-col gap-y-4">
        <Message
          v-if="errorResponse.code"
          severity="error"
          icon="pi pi-times-circle"
          class="mb-2"
        >
          <p>{{ errorResponse.message }}</p>
          <button
            v-if="errorResponse.code == 403"
            type="button"
            @click="resendEmail"
            class="text-blue-500"
          >
            Gửi email kích hoạt tài khoản
          </button>
        </Message>
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
            <Passsword
              v-model="passwordValue"
              :invalid="!!passwordMessage"
              placeholder="Mật khẩu"
              toggle-mask
              :feedback="false"
              fluid
            />
          </InputGroup>
          <Message
            v-if="passwordMessage"
            severity="error"
            variant="simple"
            size="small"
            >{{ passwordMessage }}</Message
          >
        </div>
        <Button :is-disable="loading">Đăng nhập</Button>
      </form>
      <div class="flex justify-between p-2">
        <RouterLink
          :to="{ name: FORGOT_PASSWORD_ROUTE_NAME }"
          class="text-sm text-blue-500"
          >Quên mật khẩu?</RouterLink
        >
        <RouterLink :to="{ name: REGISTER_ROUTE_NAME }" class="text-sm text-blue-500"
          >Tạo tài khoản</RouterLink
        >
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
import Passsword from "primevue/password";
import { useField, useForm } from "vee-validate";
import * as yup from "yup";
import apiClient from "@/api";
import { useRoute, useRouter } from "vue-router";
import { ref } from "vue";
import { useToast } from "primevue/usetoast";
import { useUserStore } from "@/stores/user";
import { FORGOT_PASSWORD_ROUTE_NAME, REGISTER_ROUTE_NAME } from "@/helper/constant";

const schema = yup.object({
  user_name: yup.string().required("Tên người dùng/email là bắt buộc"),
  password: yup.string().required("Mật khẩu là bắt buộc"),
});
const { handleSubmit } = useForm({
  validationSchema: schema,
});

const { value: userNameValue, errorMessage: userNameMessage } = useField("user_name");
const { value: passwordValue, errorMessage: passwordMessage } = useField("password");
const api = apiClient();
const toast = useToast();
const router = useRouter();
const route = useRoute();
const loading = ref(false);
const errorResponse = ref({
  code: undefined,
  message: "",
});
const userStore = useUserStore();

const onSubmit = handleSubmit(async (value) => {
  if (loading.value) {
    return;
  }

  loading.value = true;
  try {
    await userStore.login(value);
    const next = route.query.redirect ? decodeURIComponent(route.query.redirect) : "/";
    router.push(next);
  } catch (error) {
    if (error.status == 401) {
      errorResponse.value.code = error.status;
      errorResponse.value.message = error?.response?.data?.message;
    } else if (error.status == 403) {
      errorResponse.value.code = error.status;
      errorResponse.value.message = error?.response?.data?.message;
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
