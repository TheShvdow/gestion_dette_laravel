<template>
  <AuthenticatedLayout :user="$page.props.auth.user">
    <template #header>
      <div class="flex items-center space-x-3">
        <Link href="/boutiquier/clients" class="text-indigo-600 hover:text-indigo-800 transition-colors">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
          </svg>
        </Link>
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Détail du Client</h2>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <!-- Client Info Card -->
        <div class="bg-white/80 backdrop-blur-sm overflow-hidden shadow-lg sm:rounded-xl border border-gray-200">
          <div class="p-6">
            <div class="flex items-start justify-between">
              <div class="flex items-center space-x-4">
                <div class="flex-shrink-0 h-16 w-16 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-full flex items-center justify-center">
                  <span class="text-2xl font-bold text-white">{{ client.surname?.charAt(0)?.toUpperCase() }}</span>
                </div>
                <div>
                  <h3 class="text-2xl font-bold text-gray-900">{{ client.surname }}</h3>
                  <p class="text-gray-500">{{ client.user?.login }}</p>
                </div>
              </div>
              <span :class="[
                'px-3 py-1 text-sm font-semibold rounded-full',
                client.user?.active === 'oui' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
              ]">
                {{ client.user?.active === 'oui' ? 'Actif' : 'Inactif' }}
              </span>
            </div>

            <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
              <div class="flex items-center space-x-3">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                </svg>
                <span class="text-gray-700">{{ client.telephone }}</span>
              </div>
              <div class="flex items-center space-x-3">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <span class="text-gray-700">{{ client.adresse || 'Adresse non renseignée' }}</span>
              </div>
              <div class="flex items-center space-x-3">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                <span class="text-gray-700">{{ client.user?.role?.libelle || 'Client' }}</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
          <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl p-4 text-white shadow-lg transform hover:scale-105 transition-transform duration-200">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-blue-100 text-sm">Total Dettes</p>
                <p class="text-2xl font-bold">{{ formatMoney(stats.totalDettes) }}</p>
                <p class="text-blue-200 text-xs">FCFA</p>
              </div>
              <svg class="w-10 h-10 text-blue-200 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
          </div>

          <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl p-4 text-white shadow-lg transform hover:scale-105 transition-transform duration-200">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-green-100 text-sm">Total Payé</p>
                <p class="text-2xl font-bold">{{ formatMoney(stats.totalPaye) }}</p>
                <p class="text-green-200 text-xs">FCFA</p>
              </div>
              <svg class="w-10 h-10 text-green-200 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
          </div>

          <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-xl p-4 text-white shadow-lg transform hover:scale-105 transition-transform duration-200">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-red-100 text-sm">Reste à Payer</p>
                <p class="text-2xl font-bold">{{ formatMoney(stats.totalRestant) }}</p>
                <p class="text-red-200 text-xs">FCFA</p>
              </div>
              <svg class="w-10 h-10 text-red-200 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
          </div>

          <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl p-4 text-white shadow-lg transform hover:scale-105 transition-transform duration-200">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-orange-100 text-sm">En Cours</p>
                <p class="text-2xl font-bold">{{ stats.dettesEnCours }}</p>
                <p class="text-orange-200 text-xs">dettes</p>
              </div>
              <svg class="w-10 h-10 text-orange-200 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
          </div>

          <div class="bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl p-4 text-white shadow-lg transform hover:scale-105 transition-transform duration-200">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-emerald-100 text-sm">Soldées</p>
                <p class="text-2xl font-bold">{{ stats.dettesSoldees }}</p>
                <p class="text-emerald-200 text-xs">dettes</p>
              </div>
              <svg class="w-10 h-10 text-emerald-200 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
              </svg>
            </div>
          </div>
        </div>

        <!-- Dettes List -->
        <div class="bg-white/80 backdrop-blur-sm overflow-hidden shadow-lg sm:rounded-xl border border-gray-200">
          <div class="p-6">
            <h3 class="text-lg font-semibold mb-4 flex items-center">
              <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
              </svg>
              Historique des Dettes
            </h3>

            <div v-if="client.dettes && client.dettes.length > 0" class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Montant</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Payé</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Restant</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr v-for="dette in client.dettes" :key="dette.id" class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap">{{ formatDate(dette.date) }}</td>
                    <td class="px-6 py-4 whitespace-nowrap font-semibold">{{ formatMoney(dette.montant) }} FCFA</td>
                    <td class="px-6 py-4 whitespace-nowrap text-green-600">{{ formatMoney(dette.montantDu) }} FCFA</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <span :class="dette.montantRestant > 0 ? 'text-red-600 font-semibold' : 'text-green-600 font-semibold'">
                        {{ formatMoney(dette.montantRestant) }} FCFA
                      </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <span :class="[
                        'px-2 py-1 text-xs font-semibold rounded-full',
                        dette.status === 'Solde' ? 'bg-green-100 text-green-800' : 'bg-orange-100 text-orange-800'
                      ]">
                        {{ dette.status === 'Solde' ? 'Soldée' : 'Non soldée' }}
                      </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <Link :href="`/boutiquier/dettes/${dette.id}`" class="text-indigo-600 hover:text-indigo-900 font-medium">
                        Voir détails
                      </Link>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <div v-else class="text-center py-12">
              <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
              </svg>
              <h3 class="mt-2 text-sm font-medium text-gray-900">Aucune dette</h3>
              <p class="mt-1 text-sm text-gray-500">Ce client n'a pas encore de dettes enregistrées.</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
  client: Object,
  stats: Object,
});

const formatMoney = (value) => {
  return new Intl.NumberFormat('fr-FR').format(value || 0);
};

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('fr-FR');
};
</script>
