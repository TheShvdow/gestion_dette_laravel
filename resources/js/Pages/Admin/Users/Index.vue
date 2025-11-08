<template>
  <AuthenticatedLayout :user="$page.props.auth.user">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">Gestion des Utilisateurs</h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6">
            <div class="flex justify-between items-center mb-6">
              <h3 class="text-lg font-semibold">Liste des Utilisateurs</h3>
              <PrimaryButton @click="showCreateModal = true">
                Nouvel Utilisateur
              </PrimaryButton>
            </div>

            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nom</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Prénom</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Login</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rôle</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr v-for="user in users.data" :key="user.id">
                    <td class="px-6 py-4 whitespace-nowrap">{{ user.nom }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ user.prenom }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ user.login }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ user.role?.libelle }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <span :class="user.active ? 'text-green-600' : 'text-red-600'">
                        {{ user.active ? 'Actif' : 'Inactif' }}
                      </span>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

defineProps({
  users: Object,
});

const showCreateModal = ref(false);
</script>
