<script setup>
import { LOGIN_PATH } from "~/constants/routes";
import { NAVIGATION } from "~/constants/workspace";
const auth = useAuthStore(),
  route = useRoute(),
  mobileNav = ref(false);
const navigation = useNavigationStore();
const section = computed(
  () =>
    NAVIGATION.find((item) => route.path === item.to || route.path.startsWith(item.to + "/"))?.id ??
    "dashboard",
);
watch(
  () => route.path,
  () => {
    mobileNav.value = false;
  },
);
watch(
  () => auth.user?.id,
  (id) => {
    if (!id) void navigateTo({ path: LOGIN_PATH, query: { redirect: route.fullPath } });
  },
);
async function logout() {
  try {
    await auth.logout();
  } finally {
    await navigateTo(LOGIN_PATH);
  }
}
</script>
<template>
  <div
    v-if="auth.user"
    class="min-h-screen flex"
  >
    <LayoutWorkspaceSidebar
      :section="section"
      :user="auth.user"
      :project-count="navigation.projectCount"
      @logout="logout"
    />
    <div class="flex-1 min-w-0 md:ml-[224px]">
      <LayoutWorkspaceHeader
        v-model:mobile-nav="mobileNav"
        :section="section"
        :user="auth.user"
        @logout="logout"
      />
      <main class="p-5 lg:p-9 max-w-[1600px] mx-auto">
        <slot />
        <footer class="flex justify-between text-[9px] text-[#a8b0ab] mt-7">
          <span>Made for meaningful work.</span>
          <span>Orbit Workspace © 2026</span>
        </footer>
      </main>
    </div>
  </div>
  <div
    v-else
    class="min-h-screen grid place-items-center muted"
  >
    Opening your workspace…
  </div>
</template>
