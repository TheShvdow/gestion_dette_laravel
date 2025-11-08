<template>
  <AuthenticatedLayout :user="$page.props.auth.user">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Dashboard
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Welcome Message -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
          <div class="p-6 text-gray-900">
            <h3 class="text-lg font-semibold mb-2">
              Bienvenue, {{ $page.props.auth.user.nom }} {{ $page.props.auth.user.prenom }}
            </h3>
            <p class="text-gray-600">
              Rôle : <span class="font-semibold">{{ $page.props.auth.user.role.libelle }}</span>
            </p>
          </div>
        </div>

        <!-- Admin Stats -->
        <div v-if="isAdmin">
          <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 mb-6">
            <StatCard title="Utilisateurs" :value="stats.users" icon="users" color="indigo" />
            <StatCard title="Clients" :value="stats.clients" icon="clients" color="green" />
            <StatCard title="Articles" :value="stats.articles" icon="articles" color="yellow" />
            <StatCard title="Dettes en cours" :value="stats.dettesEnCours" icon="dettes" color="red" />
          </div>

          <div class="grid grid-cols-1 gap-5 sm:grid-cols-3 mb-6">
            <StatCard title="Total Dettes" :value="`${formatMoney(stats.totalDettes)} FCFA`" color="purple" />
            <StatCard title="Total Payé" :value="`${formatMoney(stats.totalPaye)} FCFA`" color="green" />
            <StatCard title="Total Restant" :value="`${formatMoney(stats.totalRestant)} FCFA`" color="orange" />
          </div>

          <!-- Charts -->
          <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <ChartCard title="Évolution des Dettes par Mois">
              <LineChart :data="dettesChartData" />
            </ChartCard>
            <ChartCard title="Répartition par Statut">
              <DoughnutChart :data="statutChartData" />
            </ChartCard>
          </div>
        </div>

        <!-- Boutiquier Stats -->
        <div v-if="isBoutiquier">
          <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 mb-6">
            <StatCard title="Clients" :value="stats.clients" icon="clients" color="green" />
            <StatCard title="Clients Actifs" :value="stats.clientsActifs" icon="users" color="blue" />
            <StatCard title="Articles" :value="stats.articles" icon="articles" color="yellow" />
            <StatCard title="Articles Disponibles" :value="stats.articlesDisponibles" icon="articles" color="green" />
          </div>

          <div class="grid grid-cols-1 gap-5 sm:grid-cols-3 mb-6">
            <StatCard title="Total Dettes" :value="`${formatMoney(stats.totalDettes)} FCFA`" color="purple" />
            <StatCard title="Total Payé" :value="`${formatMoney(stats.totalPaye)} FCFA`" color="green" />
            <StatCard title="Total Restant" :value="`${formatMoney(stats.totalRestant)} FCFA`" color="orange" />
          </div>

          <!-- Charts -->
          <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <ChartCard title="Évolution des Dettes et Paiements">
              <LineChart :data="dettesEtPaiementsChartData" />
            </ChartCard>
            <ChartCard title="Répartition par Statut">
              <DoughnutChart :data="statutChartData" />
            </ChartCard>
          </div>

          <div class="grid grid-cols-1 gap-6">
            <ChartCard title="Top 5 Clients avec le Plus de Dettes">
              <BarChart :data="topClientsChartData" />
            </ChartCard>
          </div>
        </div>

        <!-- Client Stats -->
        <div v-if="isClient">
          <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3 mb-6">
            <StatCard title="Dettes en cours" :value="stats.myDettes" icon="dettes" color="red" />
            <StatCard title="Dettes Soldées" :value="stats.dettesSoldees" icon="check" color="green" />
            <StatCard title="Total Restant" :value="`${formatMoney(stats.totalRestant)} FCFA`" color="orange" />
          </div>

          <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <ChartCard title="Historique des Paiements">
              <LineChart :data="paiementsChartData" />
            </ChartCard>
            <ChartCard title="Répartition par Statut">
              <DoughnutChart :data="statutChartData" />
            </ChartCard>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { computed } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import StatCard from '@/Components/StatCard.vue';
import ChartCard from '@/Components/ChartCard.vue';
import LineChart from '@/Components/Charts/LineChart.vue';
import DoughnutChart from '@/Components/Charts/DoughnutChart.vue';
import BarChart from '@/Components/Charts/BarChart.vue';

const props = defineProps({
  stats: {
    type: Object,
    default: () => ({}),
  },
  chartData: {
    type: Object,
    default: () => ({}),
  },
});

const isAdmin = computed(() => props.stats.users !== undefined);
const isBoutiquier = computed(() => props.stats.clientsActifs !== undefined);
const isClient = computed(() => props.stats.myDettes !== undefined);

const formatMoney = (value) => {
  return new Intl.NumberFormat('fr-FR').format(value || 0);
};

// Chart data for dettes evolution
const dettesChartData = computed(() => {
  if (!props.chartData.dettesParMois) return null;

  return {
    labels: props.chartData.dettesParMois.map(d => d.mois),
    datasets: [
      {
        label: 'Montant des Dettes (FCFA)',
        data: props.chartData.dettesParMois.map(d => d.total),
        borderColor: 'rgb(239, 68, 68)',
        backgroundColor: 'rgba(239, 68, 68, 0.1)',
        tension: 0.4,
      },
    ],
  };
});

// Chart data for dettes et paiements
const dettesEtPaiementsChartData = computed(() => {
  if (!props.chartData.dettesParMois) return null;

  return {
    labels: props.chartData.dettesParMois.map(d => d.mois),
    datasets: [
      {
        label: 'Dettes (FCFA)',
        data: props.chartData.dettesParMois.map(d => d.total),
        borderColor: 'rgb(239, 68, 68)',
        backgroundColor: 'rgba(239, 68, 68, 0.1)',
        tension: 0.4,
      },
      {
        label: 'Paiements (FCFA)',
        data: props.chartData.paiementsParMois?.map(d => d.total) || [],
        borderColor: 'rgb(34, 197, 94)',
        backgroundColor: 'rgba(34, 197, 94, 0.1)',
        tension: 0.4,
      },
    ],
  };
});

// Chart data for paiements (client)
const paiementsChartData = computed(() => {
  if (!props.chartData.paiementsParMois) return null;

  return {
    labels: props.chartData.paiementsParMois.map(d => d.mois),
    datasets: [
      {
        label: 'Paiements (FCFA)',
        data: props.chartData.paiementsParMois.map(d => d.total),
        borderColor: 'rgb(34, 197, 94)',
        backgroundColor: 'rgba(34, 197, 94, 0.1)',
        tension: 0.4,
      },
    ],
  };
});

// Chart data for statut
const statutChartData = computed(() => {
  if (!props.chartData.dettesParStatut) return null;

  return {
    labels: props.chartData.dettesParStatut.map(d => d.label),
    datasets: [
      {
        data: props.chartData.dettesParStatut.map(d => d.value),
        backgroundColor: [
          'rgba(239, 68, 68, 0.8)',
          'rgba(34, 197, 94, 0.8)',
        ],
        borderColor: [
          'rgb(239, 68, 68)',
          'rgb(34, 197, 94)',
        ],
        borderWidth: 1,
      },
    ],
  };
});

// Chart data for top clients
const topClientsChartData = computed(() => {
  if (!props.chartData.topClients) return null;

  return {
    labels: props.chartData.topClients.map(c => c.name),
    datasets: [
      {
        label: 'Dette Restante (FCFA)',
        data: props.chartData.topClients.map(c => c.value),
        backgroundColor: 'rgba(239, 68, 68, 0.8)',
        borderColor: 'rgb(239, 68, 68)',
        borderWidth: 1,
      },
    ],
  };
});
</script>
