export default defineNuxtConfig({
  compatibilityDate: "2026-10-04",
  modules: ["@pinia/nuxt", "@nuxtjs/tailwindcss"],
  css: ["~/assets/main.css"],
  runtimeConfig: { public: { apiBase: "/api" } },
  nitro: {
    devProxy: {
      "/api": {
        target: process.env.API_PROXY_TARGET || "http://127.0.0.1:8000/api",
        changeOrigin: true,
      },
    },
  },
  app: {
    head: {
      link: [{ rel: "icon", type: "image/svg+xml", href: "/favicon.svg" }],
      title: "Orbit · Project Management",
      meta: [
        {
          name: "description",
          content: "Your clients, projects and team, in one workspace.",
        },
      ],
    },
  },
  devtools: { enabled: false },
});
