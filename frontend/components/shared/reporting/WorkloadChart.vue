<script setup>
defineProps({ workload: { type: Array, required: true } });
</script>
<template>
  <div class="panel p-5">
    <div class="flex justify-between items-center">
      <div>
        <h2 class="font-bold text-sm">Team workload</h2>
        <p class="text-[10px] muted mt-1">Open tasks across your team</p>
      </div>
      <span class="text-[10px] muted border rounded-md px-2 py-1">Current workload</span>
    </div>
    <div class="flex h-[190px] mt-7 pl-2 gap-4">
      <div class="flex flex-col justify-between text-[9px] muted pb-6">
        <span>{{ Math.max(4, ...workload.map((w) => w.tasks_count || 0)) }}</span>
        <span>Tasks</span>
        <span>0</span>
      </div>
      <div
        class="flex-1 flex justify-around items-end border-b border-[#e9eeeb] bg-[repeating-linear-gradient(to_top,transparent,transparent_46px,#f0f3f1_47px,#f0f3f1_48px)]"
      >
        <div
          v-for="person in workload"
          :key="person.id"
          class="h-full w-full flex flex-col justify-end items-center gap-2 max-w-[120px]"
        >
          <div
            class="bg-[#8fb69a] hover:bg-[#5e9a74] w-9 sm:w-12 rounded-t-md relative"
            :style="{
              height:
                Math.max(
                  3,
                  ((person.tasks_count || 0) /
                    Math.max(4, ...workload.map((w) => w.tasks_count || 0))) *
                    145,
                ) + 'px',
            }"
          >
            <span class="absolute -top-5 text-[10px] text-[#72917c] w-full text-center">
              {{ person.tasks_count }}
            </span>
          </div>
          <span class="text-[9px] muted pb-1">{{ person.name.split(" ")[0] }}</span>
        </div>
      </div>
    </div>
  </div>
</template>
