<template>
  <Doughnut v-if="data" :data="data" :options="options" />
  <div v-else class="flex items-center justify-center h-full text-gray-500">
    Aucune donnée disponible
  </div>
</template>

<script setup>
import { Doughnut } from 'vue-chartjs';
import {
  Chart as ChartJS,
  ArcElement,
  Tooltip,
  Legend,
} from 'chart.js';

ChartJS.register(ArcElement, Tooltip, Legend);

defineProps({
  data: {
    type: Object,
    default: null,
  },
});

const options = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: {
      display: true,
      position: 'bottom',
    },
  },
};
</script>
