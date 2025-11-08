<template>
  <AuthenticatedLayout :user="$page.props.auth.user">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">Mes Dettes</h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6">
            <h3 class="text-lg font-semibold mb-4">Liste de mes Dettes</h3>

            <div v-if="dettes.data && dettes.data.length > 0" class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Montant</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Montant Payé</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Montant Restant</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr v-for="dette in dettes.data" :key="dette.id">
                    <td class="px-6 py-4 whitespace-nowrap">{{ dette.date }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ dette.montant }} FCFA</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ dette.montantDu }} FCFA</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ dette.montantRestant }} FCFA</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <span :class="dette.status === 'Solde' ? 'text-green-600 font-semibold' : 'text-orange-600 font-semibold'">
                        {{ dette.status }}
                      </span>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
            <div v-else class="text-center py-8 text-gray-500">
              Aucune dette trouvée
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

defineProps({
  dettes: Object,
});
</script>
