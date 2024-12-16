<template>
  <Header container />
  <Container>
    <div class="h-[calc(100vh-64px)] flex items-center justify-center">
      <p class="text-sky-600 text-3xl uppercase">404 - Không tìm thấy</p>
    </div>
  </Container>
</template>

<script setup>
import Container from "@/components/Container.vue";
import Header from "@/components/Header.vue";
</script>

<style lang="scss" scoped></style>
