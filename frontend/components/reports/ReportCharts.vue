<script setup>
defineProps({ stats: { type: Object, required: true } });
</script>
<template>
  <div>
    <SharedReportingStatCards :stats="stats" />
    <div class="grid xl:grid-cols-[1.55fr_1fr] gap-5 mb-7">
      <SharedReportingWorkloadChart :workload="stats.workload" />
      <SharedReportingTaskOverview :stats="stats" />
    </div>
  </div>
</template>
