<script setup>
import { FORM_LIMITS } from "~/constants/forms";
import { USER_ROLES } from "~/constants/domain";
const form = defineModel({ type: Object, required: true });
defineProps({ editingId: { type: [Number, null], required: true } });
</script>
<template>
  <div>
    <label>Email</label>
    <input
      v-model="form.email"
      type="email"
      required
    />
  </div>
  <div>
    <label>Role</label>
    <select v-model="form.role">
      <option
        v-for="r in USER_ROLES"
        :key="r"
      >
        {{ r }}
      </option>
    </select>
  </div>
  <div class="full">
    <label>
      {{ editingId ? "New password (leave blank to keep)" : "Password (12 characters minimum)" }}
    </label>
    <input
      v-model="form.password"
      type="password"
      :required="!editingId"
      :minlength="FORM_LIMITS.PASSWORD_MIN"
      autocomplete="new-password"
    />
  </div>
</template>
