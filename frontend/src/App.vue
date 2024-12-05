<template>
  <div>
    <Toast />
    <ConfirmDialog />
    <ScrollTop />
    <RouterView />
  </div>
</template>

<script setup>
import Toast from "primevue/toast";
import ConfirmDialog from "primevue/confirmdialog";
import { ScrollTop } from "primevue";
</script>

<style lang="scss" scoped></style>
