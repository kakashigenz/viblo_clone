/** @type {import('tailwindcss').Config} */
export default {
  content: ["./index.html", "./src/**/*.{js,ts,vue}"],
  theme: {
    extend: {
      screens: {
        "2xl": { max: "1535px" }, // Màn hình nhỏ hơn 1536px
        xl: { max: "1279px" }, // Màn hình nhỏ hơn 1280px
        lg: { max: "1023px" }, // Màn hình nhỏ hơn 1024px
        md: { max: "767px" }, // Màn hình nhỏ hơn 768px
        sm: { max: "639px" }, // Màn hình nhỏ hơn 640px
      },
    },
  },
  plugins: [require("tailwindcss-primeui")],
};
