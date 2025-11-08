<template>
  <AuthenticatedLayout :user="$page.props.auth.user">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">Gestion des Clients</h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Success/Error Messages -->
        <div v-if="$page.props.flash.success" class="mb-4 bg-gradient-to-r from-green-50 to-emerald-50 border-l-4 border-green-500 text-green-700 px-6 py-4 rounded-lg shadow-md animate-slideIn flex items-center">
          <svg class="w-5 h-5 mr-3 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          {{ $page.props.flash.success }}
        </div>
        <div v-if="$page.props.flash.error" class="mb-4 bg-gradient-to-r from-red-50 to-rose-50 border-l-4 border-red-500 text-red-700 px-6 py-4 rounded-lg shadow-md animate-slideIn flex items-center">
          <svg class="w-5 h-5 mr-3 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          {{ $page.props.flash.error }}
        </div>

        <div class="bg-white/80 backdrop-blur-sm overflow-hidden shadow-lg sm:rounded-xl border border-gray-200 hover:shadow-xl transition-shadow duration-300">
          <div class="p-6">
            <!-- Header with Actions -->
            <div class="flex justify-between items-center mb-6">
              <h3 class="text-lg font-semibold">Liste des Clients</h3>
              <PrimaryButton @click="showCreateModal = true">
                Nouveau Client
              </PrimaryButton>
            </div>

            <!-- Filters -->
            <div class="mb-4 grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <InputLabel for="search" value="Rechercher" />
                <TextInput
                  id="search"
                  v-model="searchForm.search"
                  type="text"
                  class="mt-1 block w-full"
                  placeholder="Rechercher par nom, téléphone, adresse..."
                  @input="searchClients"
                />
              </div>
              <div>
                <InputLabel for="actif" value="Statut" />
                <select
                  id="actif"
                  v-model="searchForm.actif"
                  class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/50 rounded-lg shadow-sm transition-all duration-200 hover:border-indigo-300"
                  @change="searchClients"
                >
                  <option value="">Tous</option>
                  <option value="oui">Actif</option>
                  <option value="non">Inactif</option>
                </select>
              </div>
            </div>

            <!-- Clients Table -->
            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nom</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Téléphone</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Adresse</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Login</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr v-for="client in clients.data" :key="client.id">
                    <td class="px-6 py-4 whitespace-nowrap font-semibold">{{ client.surname }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ client.telephone }}</td>
                    <td class="px-6 py-4">{{ client.adresse || 'N/A' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ client.user?.login }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <span :class="client.user?.active ? 'text-green-600 font-semibold' : 'text-red-600 font-semibold'">
                        {{ client.user?.active ? 'Actif' : 'Inactif' }}
                      </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm space-x-2">
                      <Link :href="`/boutiquier/clients/${client.id}`" class="text-blue-600 hover:text-blue-900">
                        Voir
                      </Link>
                      <button @click="openEditModal(client)" class="text-indigo-600 hover:text-indigo-900">
                        Modifier
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <!-- Pagination -->
            <Pagination
              v-if="clients.links"
              class="mt-4"
              :links="clients.links"
              :from="clients.from"
              :to="clients.to"
              :total="clients.total"
            />
          </div>
        </div>
      </div>
    </div>

    <!-- Create Client Modal -->
    <Modal :show="showCreateModal" @close="closeCreateModal">
      <div class="p-6">
        <h3 class="text-lg font-semibold mb-4">Nouveau Client</h3>

        <form @submit.prevent="createClient">
          <div class="space-y-4">
            <div>
              <InputLabel for="surname" value="Nom complet *" />
              <TextInput
                id="surname"
                v-model="createForm.surname"
                type="text"
                class="mt-1 block w-full"
                required
              />
              <InputError :message="createForm.errors.surname" class="mt-2" />
            </div>

            <div>
              <InputLabel for="telephone" value="Téléphone *" />
              <TextInput
                id="telephone"
                v-model="createForm.telephone"
                type="text"
                class="mt-1 block w-full"
                required
              />
              <InputError :message="createForm.errors.telephone" class="mt-2" />
            </div>

            <div>
              <InputLabel for="adresse" value="Adresse" />
              <TextInput
                id="adresse"
                v-model="createForm.adresse"
                type="text"
                class="mt-1 block w-full"
              />
              <InputError :message="createForm.errors.adresse" class="mt-2" />
            </div>

            <div class="border-t pt-4">
              <p class="text-sm text-gray-600 mb-4">Informations de connexion</p>

              <div class="space-y-4">
                <div>
                  <InputLabel for="login" value="Login *" />
                  <TextInput
                    id="login"
                    v-model="createForm.login"
                    type="text"
                    class="mt-1 block w-full"
                    required
                  />
                  <InputError :message="createForm.errors.login" class="mt-2" />
                </div>

                <div>
                  <InputLabel for="password" value="Mot de passe *" />
                  <TextInput
                    id="password"
                    v-model="createForm.password"
                    type="password"
                    class="mt-1 block w-full"
                    required
                  />
                  <InputError :message="createForm.errors.password" class="mt-2" />
                  <p class="text-xs text-gray-500 mt-1">Minimum 8 caractères</p>
                </div>
              </div>
            </div>
          </div>

          <div class="mt-6 flex justify-end space-x-3">
            <SecondaryButton type="button" @click="closeCreateModal">Annuler</SecondaryButton>
            <PrimaryButton type="submit" :disabled="createForm.processing">
              Créer le Client
            </PrimaryButton>
          </div>
        </form>
      </div>
    </Modal>

    <!-- Edit Client Modal -->
    <Modal :show="showEditModal" @close="closeEditModal">
      <div class="p-6">
        <h3 class="text-lg font-semibold mb-4">Modifier le Client</h3>

        <form @submit.prevent="updateClient">
          <div class="space-y-4">
            <div>
              <InputLabel for="edit_surname" value="Nom complet *" />
              <TextInput
                id="edit_surname"
                v-model="editForm.surname"
                type="text"
                class="mt-1 block w-full"
                required
              />
              <InputError :message="editForm.errors.surname" class="mt-2" />
            </div>

            <div>
              <InputLabel for="edit_telephone" value="Téléphone *" />
              <TextInput
                id="edit_telephone"
                v-model="editForm.telephone"
                type="text"
                class="mt-1 block w-full"
                required
              />
              <InputError :message="editForm.errors.telephone" class="mt-2" />
            </div>

            <div>
              <InputLabel for="edit_adresse" value="Adresse" />
              <TextInput
                id="edit_adresse"
                v-model="editForm.adresse"
                type="text"
                class="mt-1 block w-full"
              />
              <InputError :message="editForm.errors.adresse" class="mt-2" />
            </div>

            <div>
              <InputLabel for="edit_active" value="Statut *" />
              <select
                id="edit_active"
                v-model="editForm.active"
                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/50 rounded-lg shadow-sm transition-all duration-200 hover:border-indigo-300"
                required
              >
                <option :value="true">Actif</option>
                <option :value="false">Inactif</option>
              </select>
              <InputError :message="editForm.errors.active" class="mt-2" />
            </div>
          </div>

          <div class="mt-6 flex justify-end space-x-3">
            <SecondaryButton type="button" @click="closeEditModal">Annuler</SecondaryButton>
            <PrimaryButton type="submit" :disabled="editForm.processing">
              Mettre à jour
            </PrimaryButton>
          </div>
        </form>
      </div>
    </Modal>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, reactive } from 'vue';
import { Link, useForm, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import Modal from '@/Components/Modal.vue';
import Pagination from '@/Components/Pagination.vue';

const props = defineProps({
  clients: Object,
  filters: Object,
});

const showCreateModal = ref(false);
const showEditModal = ref(false);

const createForm = useForm({
  surname: '',
  telephone: '',
  adresse: '',
  login: '',
  password: '',
});

const editForm = useForm({
  id: null,
  surname: '',
  telephone: '',
  adresse: '',
  active: true,
});

const searchForm = reactive({
  search: props.filters?.search || '',
  actif: props.filters?.actif || '',
});

const searchClients = () => {
  router.get('/boutiquier/clients', searchForm, {
    preserveState: true,
    preserveScroll: true,
  });
};

const createClient = () => {
  createForm.post('/boutiquier/clients', {
    preserveScroll: true,
    onSuccess: () => closeCreateModal(),
  });
};

const openEditModal = (client) => {
  editForm.id = client.id;
  editForm.surname = client.surname;
  editForm.telephone = client.telephone;
  editForm.adresse = client.adresse || '';
  editForm.active = client.user?.active || true;
  showEditModal.value = true;
};

const updateClient = () => {
  editForm.put(`/boutiquier/clients/${editForm.id}`, {
    preserveScroll: true,
    onSuccess: () => closeEditModal(),
  });
};

const closeCreateModal = () => {
  showCreateModal.value = false;
  createForm.reset();
  createForm.clearErrors();
};

const closeEditModal = () => {
  showEditModal.value = false;
  editForm.reset();
  editForm.clearErrors();
};
</script>
