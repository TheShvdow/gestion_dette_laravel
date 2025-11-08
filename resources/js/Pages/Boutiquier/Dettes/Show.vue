<template>
  <AuthenticatedLayout :user="$page.props.auth.user">
    <template #header>
      <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Détails de la Dette</h2>
        <Link href="/boutiquier/dettes" class="text-indigo-600 hover:text-indigo-900">
          ← Retour à la liste
        </Link>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <!-- Success/Error Messages -->
        <div v-if="$page.props.flash.success" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
          {{ $page.props.flash.success }}
        </div>
        <div v-if="$page.props.flash.error" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
          {{ $page.props.flash.error }}
        </div>

        <!-- Dette Information -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6">
            <h3 class="text-lg font-semibold mb-4">Informations de la Dette</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <p class="text-sm text-gray-600">Client</p>
                <p class="font-semibold text-lg">{{ dette.client?.surname }}</p>
                <p class="text-sm text-gray-500">{{ dette.client?.telephone }}</p>
              </div>

              <div>
                <p class="text-sm text-gray-600">Date</p>
                <p class="font-semibold text-lg">{{ formatDate(dette.date) }}</p>
              </div>

              <div>
                <p class="text-sm text-gray-600">Montant Total</p>
                <p class="font-semibold text-lg">{{ formatMoney(dette.montant) }} FCFA</p>
              </div>

              <div>
                <p class="text-sm text-gray-600">Montant Payé</p>
                <p class="font-semibold text-lg text-green-600">{{ formatMoney(dette.montantDu) }} FCFA</p>
              </div>

              <div>
                <p class="text-sm text-gray-600">Montant Restant</p>
                <p class="font-semibold text-lg text-red-600">{{ formatMoney(dette.montantRestant) }} FCFA</p>
              </div>

              <div>
                <p class="text-sm text-gray-600">Statut</p>
                <span :class="dette.status === 'Solde' ? 'inline-block px-3 py-1 bg-green-100 text-green-800 rounded-full font-semibold' : 'inline-block px-3 py-1 bg-orange-100 text-orange-800 rounded-full font-semibold'">
                  {{ dette.status === 'Solde' ? 'Soldé' : 'Non Soldé' }}
                </span>
              </div>
            </div>

            <div v-if="dette.status !== 'Solde'" class="mt-6">
              <PrimaryButton @click="showPaiementModal = true">
                Enregistrer un Paiement
              </PrimaryButton>
            </div>
          </div>
        </div>

        <!-- Articles -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6">
            <h3 class="text-lg font-semibold mb-4">Articles</h3>

            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Article</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Quantité</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Prix Unitaire</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr v-for="article in dette.articles" :key="article.id">
                    <td class="px-6 py-4 whitespace-nowrap">{{ article.libelle }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ article.pivot.qte }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ formatMoney(article.pivot.prix) }} FCFA</td>
                    <td class="px-6 py-4 whitespace-nowrap font-semibold">{{ formatMoney(article.pivot.qte * article.pivot.prix) }} FCFA</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- Paiements History -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6">
            <h3 class="text-lg font-semibold mb-4">Historique des Paiements</h3>

            <div v-if="dette.paiements && dette.paiements.length > 0" class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Montant</th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr v-for="paiement in dette.paiements" :key="paiement.id">
                    <td class="px-6 py-4 whitespace-nowrap">{{ formatDate(paiement.date) }}</td>
                    <td class="px-6 py-4 whitespace-nowrap font-semibold text-green-600">{{ formatMoney(paiement.montant) }} FCFA</td>
                  </tr>
                </tbody>
              </table>
            </div>
            <div v-else class="text-center py-8 text-gray-500">
              Aucun paiement enregistré pour cette dette
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Paiement Modal -->
    <Modal :show="showPaiementModal" @close="closePaiementModal">
      <div class="p-6">
        <h3 class="text-lg font-semibold mb-4">Enregistrer un Paiement</h3>

        <form @submit.prevent="addPaiement">
          <div class="space-y-4">
            <div>
              <p class="text-sm text-gray-600 mb-2">
                Client: <span class="font-semibold">{{ dette.client?.surname }}</span>
              </p>
              <p class="text-sm text-gray-600 mb-2">
                Montant total: <span class="font-semibold">{{ formatMoney(dette.montant) }} FCFA</span>
              </p>
              <p class="text-sm text-gray-600 mb-4">
                Montant restant: <span class="font-semibold text-red-600">{{ formatMoney(dette.montantRestant) }} FCFA</span>
              </p>
            </div>

            <div>
              <InputLabel for="montant_paiement" value="Montant du paiement (FCFA) *" />
              <TextInput
                id="montant_paiement"
                v-model="paiementForm.montant"
                type="number"
                step="0.01"
                :max="dette.montantRestant"
                class="mt-1 block w-full"
                required
              />
              <InputError :message="paiementForm.errors.montant" class="mt-2" />
            </div>
          </div>

          <div class="mt-6 flex justify-end space-x-3">
            <SecondaryButton @click="closePaiementModal">Annuler</SecondaryButton>
            <PrimaryButton :disabled="paiementForm.processing">
              Enregistrer le Paiement
            </PrimaryButton>
          </div>
        </form>
      </div>
    </Modal>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import Modal from '@/Components/Modal.vue';

const props = defineProps({
  dette: Object,
});

const showPaiementModal = ref(false);

const paiementForm = useForm({
  montant: '',
});

const formatMoney = (value) => {
  return new Intl.NumberFormat('fr-FR').format(value || 0);
};

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('fr-FR');
};

const addPaiement = () => {
  paiementForm.post(`/boutiquier/dettes/${props.dette.id}/paiement`, {
    preserveScroll: true,
    onSuccess: () => closePaiementModal(),
  });
};

const closePaiementModal = () => {
  showPaiementModal.value = false;
  paiementForm.reset();
  paiementForm.clearErrors();
};
</script>
