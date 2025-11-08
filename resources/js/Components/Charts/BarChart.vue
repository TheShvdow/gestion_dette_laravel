<template>
  <Bar v-if="data" :data="data" :options="options" />
  <div v-else class="flex items-center justify-center h-full text-gray-500">
    Aucune donnée disponible
  </div>
</template>

<script setup>
import { Bar } from 'vue-chartjs';
import {
  Chart as ChartJS,
  CategoryScale,
  LinearScale,
  BarElement,
  Title,
  Tooltip,
  Legend,
} from 'chart.js';

ChartJS.register(
  CategoryScale,
  LinearScale,
  BarElement,
  Title,
  Tooltip,
  Legend
);

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
      display: false,
    },
  },
  scales: {
    y: {
      beginAtZero: true,
      ticks: {
        callback: function(value) {
          return new Intl.NumberFormat('fr-FR').format(value) + ' FCFA';
        },
      },
    },
  },
};
</script>
