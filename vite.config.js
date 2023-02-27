import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import path from "path";

export default defineConfig({
  plugins: [
    laravel({
      input: ["resources/css/app.css", "resources/js/app.js"],
      refresh: ["app/**", "resources/views/**", "routes/**"],
    }),
  ],
  resolve: {
    alias: {
      "~fortawesome": path.resolve("node_modules/@fortawesome/fontawesome-pro"),
    },
  },
});
