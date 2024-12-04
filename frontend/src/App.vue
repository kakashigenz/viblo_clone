<template>
  <div>
    <Toast />
    <ConfirmDialog />
    <RouterView />
  </div>
</template>

<script setup>
import Toast from "primevue/toast";
import ConfirmDialog from "primevue/confirmdialog";
</script>

<style lang="scss" scoped></style>
