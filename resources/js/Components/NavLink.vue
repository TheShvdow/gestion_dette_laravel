<template>
  <Link
    :href="href"
    :class="classes"
  >
    <slot />
  </Link>
</template>

<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
  href: String,
  active: Boolean,
});

const classes = computed(() =>
  props.active
    ? 'inline-flex items-center px-3 py-2 border-b-2 border-indigo-500 text-sm font-medium leading-5 text-indigo-700 focus:outline-none focus:border-indigo-700 transition-all duration-200 ease-in-out bg-gradient-to-t from-indigo-50/50 to-transparent'
    : 'inline-flex items-center px-3 py-2 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-600 hover:text-indigo-700 hover:border-indigo-300 hover:bg-gradient-to-t hover:from-indigo-50/30 hover:to-transparent focus:outline-none focus:text-indigo-700 focus:border-indigo-300 transition-all duration-200 ease-in-out'
);
</script>
