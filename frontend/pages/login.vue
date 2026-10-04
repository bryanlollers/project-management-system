<script setup>
import { getLoginRedirect } from "~/utils/navigation";
definePageMeta({ layout: false });
useHead({ title: "Sign in · Orbit" });
const auth = useAuthStore(),
  route = useRoute();
watch(
  () => auth.user?.id,
  (id) => {
    if (id) void navigateTo(getLoginRedirect(route.query.redirect));
  },
);
</script>
<template><AuthLoginView /></template>
