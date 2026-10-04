<script setup>
defineProps({
  count: { type: Number, required: true },
  pagination: { type: Object, required: false },
  page: { type: Number, required: false },
  emptyTitle: { type: String, required: true },
  emptyHint: { type: String, required: false },
});
const emit = defineEmits(["page"]);
</script>
<template>
  <div class="panel overflow-hidden">
    <slot name="title" />
    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <slot name="head" />
          </tr>
        </thead>
        <tbody>
          <slot />
        </tbody>
      </table>
    </div>
    <div
      v-if="!count"
      class="p-12 text-center muted"
    >
      <p>{{ emptyTitle }}</p>
      <p
        v-if="emptyHint"
        class="text-xs mt-2"
      >
        {{ emptyHint }}
      </p>
    </div>
    <UiPagination
      v-if="pagination"
      :meta="pagination"
      :page="page || 1"
      @change="emit('page', $event)"
    />
  </div>
</template>
