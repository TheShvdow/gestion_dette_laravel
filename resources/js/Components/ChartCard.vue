<template>
  <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
    <div class="p-6">
      <h4 class="text-lg font-semibold text-gray-800 mb-4">{{ title }}</h4>
      <div class="chart-container">
        <slot />
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  title: {
    type: String,
    required: true,
  },
});
</script>

<style scoped>
.chart-container {
  position: relative;
  height: 300px;
}
</style>
