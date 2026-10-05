<script setup>
import { ArrowRight, Layers } from "lucide-vue-next";
import { getErrorMessage } from "~/utils/errors";
import { DEMO_EMAILS, DEMO_PASSWORD } from "~/constants/auth";
const auth = useAuthStore();
defineProps({ sessionError: { type: String, required: false } });
const email = ref(DEMO_EMAILS[0]),
  password = ref(DEMO_PASSWORD),
  saving = ref(false),
  error = ref("");
async function login() {
  saving.value = true;
  error.value = "";
  try {
    await auth.login(email.value, password.value);
  } catch (cause) {
    error.value = getErrorMessage(cause);
  } finally {
    saving.value = false;
  }
}
</script>
<template>
  <div class="min-h-screen grid md:grid-cols-2 bg-white">
    <div class="hidden md:flex bg-[#e8f1eb] p-16 flex-col justify-between">
      <div class="brand font-extrabold text-3xl text-[#247a50] flex gap-3 items-center">
        <Layers :size="32" />
        orbit
        <span class="text-xs font-medium uppercase tracking-widest text-[#7f9f8a]">Workspace</span>
      </div>
      <div>
        <p class="text-xs tracking-widest uppercase text-[#6c947a] mb-5">
          A little clarity goes a long way
        </p>
        <h1 class="text-5xl font-bold leading-tight max-w-lg">
          Great work starts
          <br />
          with a clear plan.
        </h1>
        <p class="text-[#789183] mt-6 text-lg max-w-sm">
          Bring your clients, projects, and people together. Make room for what matters.
        </p>
      </div>
      <p class="text-xs text-[#789183]">Built for teams that move things forward.</p>
    </div>
    <form
      class="p-8 max-w-md w-full m-auto"
      @submit.prevent="login"
    >
      <div class="md:hidden brand text-3xl font-bold text-[#247a50] mb-12">orbit</div>
      <p class="muted mb-2">Your work, connected.</p>
      <h1 class="text-3xl font-bold mb-8">Welcome back</h1>
      <label for="email">Email address</label>
      <input
        id="email"
        v-model="email"
        type="email"
        required
        autocomplete="username"
        class="mb-5"
      />
      <label for="password">Password</label>
      <input
        id="password"
        v-model="password"
        type="password"
        required
        autocomplete="current-password"
        class="mb-6"
      />
      <p
        v-if="error || sessionError"
        role="alert"
        class="text-red-600 mb-4"
      >
        {{ error || sessionError }}
      </p>
      <button
        class="btn primary w-full"
        :disabled="saving"
      >
        {{ saving ? "Signing in…" : "Sign in to your workspace" }}
        <ArrowRight :size="16" />
      </button>
      <p class="text-xs muted mt-6 leading-relaxed">
        Demo accounts: {{ DEMO_EMAILS.join(", ") }}
        <br />
        Password:
        {{ DEMO_PASSWORD }}
      </p>
    </form>
  </div>
</template>
