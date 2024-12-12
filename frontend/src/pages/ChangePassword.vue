<template>
  <div class="px-6">
    <h5 class="text-xl">Đổi mật khẩu</h5>
    <p class="text-sm mt-3">
      Thay đổi mật khẩu cho tài khoản của bạn. Bạn nên đặt mật khẩu mạnh để chặn những
      truy cập trái phép vào tài khoản của mình.
    </p>
    <form class="mt-5">
      <div class="flex flex-col gap-y-2 mb-4">
        <label for="password"
          ><span class="text-red-500">*</span> Mật khẩu hiện tại</label
        >
        <Password :feedback="false" toggle-mask id="password" size="small" fluid />
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
        />
      </div>
      <div class="flex flex-col gap-y-2 mb-4">
        <label for="confirmation_password"
          ><span class="text-red-500">*</span> Nhập lại mật khẩu</label
        >
        <Password
          id="confirmation_password"
          size="small"
          fluid
          toggle-mask
          :feedback="false"
        />
      </div>
      <div class="flex justify-end">
        <Button variant="primary">Cập nhật</Button>
      </div>
    </form>
  </div>
</template>

<script setup>
import Button from "@/components/Button.vue";
import { Password } from "primevue";
</script>

<style lang="scss" scoped></style>
