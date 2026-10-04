<script setup>
import { X } from "lucide-vue-next";
const form = defineModel({ type: Object, required: true });
</script>
<template>
  <div>
    <label>Company</label>
    <input v-model="form.company" />
  </div>
  <div>
    <label>Email</label>
    <input
      v-model="form.email"
      type="email"
      required
    />
  </div>
  <div>
    <label>Phone</label>
    <input v-model="form.phone" />
  </div>
  <div class="col-span-full">
    <label>Notes</label>
    <textarea
      class="resize-y max-h-[min(30dvh,240px)] overflow-y-auto"
      v-model="form.notes"
      rows="3"
    />
  </div>
  <div class="col-span-full">
    <div class="flex justify-between mb-3">
      <label>Client contacts</label>
      <button
        type="button"
        class="text-xs text-green-700"
        @click="form.contacts.push({ name: '', email: '', phone: '' })"
      >
        + Add contact
      </button>
    </div>
    <UiScrollRegion label="Client contact fields">
      <div
        v-for="(contact, i) in form.contacts"
        :key="i"
        class="flex gap-2 mb-2"
      >
        <input
          v-model="contact.name"
          placeholder="Name"
          required
          aria-label="Contact name"
        />
        <input
          v-model="contact.email"
          type="email"
          placeholder="Email"
          required
          aria-label="Contact email"
        />
        <input
          v-model="contact.phone"
          placeholder="Phone"
          aria-label="Contact phone"
        />
        <button
          type="button"
          @click="form.contacts.splice(i, 1)"
          aria-label="Remove contact"
        >
          <X :size="14" />
        </button>
      </div>
    </UiScrollRegion>
  </div>
</template>
