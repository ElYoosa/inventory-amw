import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";

export default defineConfig({
  server: {
    host: "0.0.0.0", // biar bisa diakses dari luar (via tunnel)
    port: 5173, // port default vite
    hmr: {
      host: "localhost", // ganti jadi 'localhost' agar HMR tidak salah alamat
    },
  },
  plugins: [
    laravel({
      input: ["resources/css/app.css", "resources/js/app.js"],
      refresh: true,
    }),
  ],
});
