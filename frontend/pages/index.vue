<script setup>
import { DEFAULT_WORKSPACE_PATH } from "~/constants/routes";
import { HTTP_STATUS } from "~/constants/api";
definePageMeta({
  middleware: () => navigateTo(DEFAULT_WORKSPACE_PATH, { redirectCode: HTTP_STATUS.FOUND }),
});
</script>
<template><div /></template>
