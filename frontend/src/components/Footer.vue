<template>
  <footer class="bg-[#0b1a33]">
    <Container class="text-white">
      <div class="main-footer">
        <div class="source">
          <h4 class="uppercase font-bold mb-4">Tài nguyên</h4>
          <div class="flex gap-x-12 text-sm text-gray-300">
            <ul class="flex flex-col gap-y-3">
              <li>
                <a href="#">Bài viết</a>
              </li>
              <li>
                <a href="#">Câu hỏi</a>
              </li>
              <li>
                <a href="#">Videos</a>
              </li>
              <li>
                <a href="#">Thảo luận</a>
              </li>
              <li><a href="#">Công cụ</a></li>
              <a href="#">
                <li>Trạng thái hệ thống</li>
              </a>
            </ul>
            <ul class="flex flex-col gap-y-3">
              <li><a href="#">Tổ chức</a></li>
              <li><a href="#">Tags</a></li>
              <li><a href="#">Tác giả</a></li>
              <li><a href="#">Đề xuất hệ thống</a></li>
              <li><a href="#">Machine Learning</a></li>
            </ul>
          </div>
        </div>
        <div class="service">
          <h4 class="uppercase font-bold mb-4">Dịch vụ</h4>
          <ul class="flex flex-col gap-y-3 text-sm text-gray-300">
            <li>
              <a href="#" class="flex items-center gap-x-2">
                <img src="/favicon.ico" alt="" width="24" height="24" />
                Viblo
              </a>
            </li>
            <li>
              <a href="#" class="flex items-center gap-x-2">
                <img src="/images/viblo-code.png" alt="" width="24" height="24" />
                Viblo Code
              </a>
            </li>
            <li>
              <a href="#" class="flex items-center gap-x-2">
                <img src="/images/viblo-ctf.png" alt="" width="24" height="24" />
                Viblo CTF
              </a>
            </li>
            <li>
              <a href="#" class="flex items-center gap-x-2">
                <img src="/images/viblo-cv.png" alt="" width="24" height="24" />
                Viblo CV
              </a>
            </li>
            <li>
              <a href="#" class="flex items-center gap-x-2">
                <img src="/images/viblo-learn.png" alt="" width="24" height="24" />
                Viblo Learning
              </a>
            </li>
            <li>
              <a href="#" class="flex items-center gap-x-2">
                <img src="/images/viblo-partner.png" alt="" width="24" height="24" />
                Viblo Partner
              </a>
            </li>
            <li>
              <a href="#" class="flex items-center gap-x-2">
                <img src="/images/viblo-battle.png" alt="" width="24" height="24" />
                Viblo Battle
              </a>
            </li>
            <li>
              <a href="#" class="flex items-center gap-x-2">
                <img src="/images/viblo-interview.ico" alt="" width="24" height="24" />
                Viblo Interview
              </a>
            </li>
          </ul>
        </div>
        <div class="link">
          <h4 class="uppercase font-bold mb-2">Ứng dụng di động</h4>
          <div class="flex items-center mb-2">
            <div class="flex flex-col">
              <a href="#" class="w-[150px]">
                <img
                  src="/images/en_badge_web_generic.png"
                  alt=""
                  class="w-full h-auto object-cover"
                />
              </a>
              <a href="#" class="w-[130px] m-2">
                <img
                  src="/images/app-store-badge.8c4986ee4828b47d16f5cd694ef065f2.svg"
                  alt=""
                  class="w-full h-auto object-cover"
                />
              </a>
            </div>
            <a href="#" class="block w-[100px] h-[100px]">
              <img src="/images/download.png" alt="" class="w-full h-auto object-cover" />
            </a>
          </div>
          <div class="">
            <h4 class="uppercase font-bold mb-4">Liên kết</h4>
            <div class="flex gap-x-4">
              <a href="#">
                <i class="pi pi-facebook" style="font-size: 22px"></i>
              </a>
              <a href="#">
                <i class="pi pi-github" style="font-size: 22px"></i>
              </a>
              <a href="#">
                <i class="pi pi-discord" style="font-size: 22px"></i>
              </a>
              <a href="#">
                <i class="pi pi-youtube" style="font-size: 22px"></i>
              </a>
            </div>
          </div>
        </div>
        <div class="copyright">
          <span class="text-sm text-gray-300">
            &copy; {{ year }} <b class="font-bold">Viblo.</b> All rights reserved.
          </span>
          <ul class="flex gap-x-4">
            <li>
              <a href="#" class="text-sm text-gray-300"> Về chúng tôi </a>
            </li>
            <li>
              <a href="#" class="text-sm text-gray-300"> Phản hồi </a>
            </li>
            <li>
              <a href="#" class="text-sm text-gray-300"> Giúp đỡ </a>
            </li>
            <li>
              <a href="#" class="text-sm text-gray-300"> FAQs </a>
            </li>
            <li>
              <a href="#" class="text-sm text-gray-300"> RSS </a>
            </li>
            <li>
              <a href="#" class="text-sm text-gray-300"> Điều khoản </a>
            </li>
          </ul>
        </div>
      </div>
    </Container>
  </footer>
</template>

<script setup>
import Container from "./Container.vue";

const year = new Date().getFullYear();
</script>

<style lang="css" scoped>
.main-footer {
  padding: 16px;
  display: grid;
  grid-template-areas:
    "source service link"
    "source service link"
    "copyright copyright copyright";
}

.source {
  grid-area: source;
}

.service {
  grid-area: service;
}

.link {
  grid-area: link;
}

.copyright {
  grid-area: copyright;
  margin-top: 24px;
  border-top: 1px #555 solid;
  padding: 22px;
  display: flex;
  justify-content: space-between;
}

a:hover {
  color: #eee;
}
</style>
