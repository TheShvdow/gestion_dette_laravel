<template>
  <AppLayout :user="$page.props.auth?.user">
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
      <div class="p-6">
        <h1 class="text-2xl font-bold text-gray-900 mb-4">
          Bienvenue dans Gestion de Dettes
        </h1>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8">
          <!-- Card pour Admin -->
          <div v-if="userRole === 'Admin'" class="bg-blue-50 p-6 rounded-lg">
            <h2 class="text-lg font-semibold text-blue-900 mb-2">
              Administration
            </h2>
            <p class="text-blue-700 mb-4">Gestion des utilisateurs et du système</p>
            <Link href="/admin/users" class="inline-block bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
              Accéder
            </Link>
          </div>

          <!-- Card pour Boutiquier -->
          <div v-if="userRole === 'Boutiquier' || userRole === 'Admin'" class="bg-green-50 p-6 rounded-lg">
            <h2 class="text-lg font-semibold text-green-900 mb-2">
              Gestion Boutique
            </h2>
            <p class="text-green-700 mb-4">Clients, Articles et Dettes</p>
            <Link href="/boutiquier/clients" class="inline-block bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
              Accéder
            </Link>
          </div>

          <!-- Card pour Client -->
          <div v-if="userRole === 'Client'" class="bg-purple-50 p-6 rounded-lg">
            <h2 class="text-lg font-semibold text-purple-900 mb-2">
              Mes Dettes
            </h2>
            <p class="text-purple-700 mb-4">Voir mes dettes et paiements</p>
            <Link href="/client/dettes" class="inline-block bg-purple-600 text-white px-4 py-2 rounded hover:bg-purple-700">
              Accéder
            </Link>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
  auth: Object,
});

const userRole = computed(() => props.auth?.user?.role?.libelle || 'Guest');
</script>
