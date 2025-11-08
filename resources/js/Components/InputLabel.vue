<template>
  <label :for="for" class="block font-medium text-sm text-gray-700">
    <slot>{{ value }}</slot>
  </label>
</template>

<script setup>
defineProps({
  for: String,
  value: String,
});
</script>
