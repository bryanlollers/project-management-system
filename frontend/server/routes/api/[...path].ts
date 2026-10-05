export default defineEventHandler((event) => {
  const base = process.env.API_PROXY_TARGET || "http://127.0.0.1:8000/api";
  const path = event.path.replace(/^\/api/, "");
  return proxyRequest(event, base.replace(/\/$/, "") + path, {
    headers: { Accept: "application/json" },
  });
});
