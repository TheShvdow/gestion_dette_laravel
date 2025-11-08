<template>
  <div v-if="links && links.length > 3" class="flex justify-between items-center">
    <div class="text-sm text-gray-700">
      Affichage de {{ from }} à {{ to }} sur {{ total }} résultats
    </div>
    <div class="flex space-x-1">
      <template v-for="(link, index) in links" :key="index">
        <Link
          v-if="link.url"
          :href="link.url"
          :class="[
            'px-3 py-2 border rounded',
            link.active ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-gray-700 hover:bg-gray-50 border-gray-300',
          ]"
          v-html="link.label"
        />
        <span
          v-else
          :class="[
            'px-3 py-2 border rounded opacity-50 cursor-not-allowed',
            'bg-white text-gray-400 border-gray-300',
          ]"
          v-html="link.label"
        />
      </template>
    </div>
  </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
  links: Array,
  from: Number,
  to: Number,
  total: Number,
});
</script>
