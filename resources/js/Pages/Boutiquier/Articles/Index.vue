<template>
  <AuthenticatedLayout :user="$page.props.auth.user">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">Gestion des Articles</h2>
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
              <h3 class="text-lg font-semibold">Liste des Articles</h3>
              <PrimaryButton @click="showCreateModal = true">
                Nouvel Article
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
                  placeholder="Rechercher par libellé..."
                  @input="searchArticles"
                />
              </div>
              <div>
                <InputLabel for="disponible" value="Disponibilité" />
                <select
                  id="disponible"
                  v-model="searchForm.disponible"
                  class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/50 rounded-lg shadow-sm transition-all duration-200 hover:border-indigo-300"
                  @change="searchArticles"
                >
                  <option value="">Tous</option>
                  <option value="oui">Disponible</option>
                  <option value="non">Rupture de stock</option>
                </select>
              </div>
            </div>

            <!-- Articles Table -->
            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Libellé</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Prix</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Quantité</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr v-for="article in articles.data" :key="article.id">
                    <td class="px-6 py-4 whitespace-nowrap">{{ article.libelle }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ formatMoney(article.prix) }} FCFA</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <span :class="article.quantite > 0 ? 'text-green-600 font-semibold' : 'text-red-600 font-semibold'">
                        {{ article.quantite }}
                      </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <span :class="article.quantite > 0 ? 'text-green-600' : 'text-red-600'">
                        {{ article.quantite > 0 ? 'Disponible' : 'Rupture' }}
                      </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm space-x-2">
                      <button @click="openUpdateStockModal(article)" class="text-blue-600 hover:text-blue-900">
                        Stock
                      </button>
                      <button @click="openEditModal(article)" class="text-indigo-600 hover:text-indigo-900">
                        Modifier
                      </button>
                      <button @click="confirmDelete(article)" class="text-red-600 hover:text-red-900">
                        Supprimer
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <!-- Pagination -->
            <Pagination
              v-if="articles.links"
              class="mt-4"
              :links="articles.links"
              :from="articles.from"
              :to="articles.to"
              :total="articles.total"
            />
          </div>
        </div>
      </div>
    </div>

    <!-- Create/Edit Modal -->
    <Modal :show="showCreateModal || showEditModal" @close="closeModal">
      <div class="p-6">
        <h3 class="text-lg font-semibold mb-4">{{ showEditModal ? 'Modifier l\'Article' : 'Nouvel Article' }}</h3>

        <form @submit.prevent="showEditModal ? updateArticle() : createArticle()">
          <div class="space-y-4">
            <div>
              <InputLabel for="libelle" value="Libellé *" />
              <TextInput
                id="libelle"
                v-model="form.libelle"
                type="text"
                class="mt-1 block w-full"
                required
              />
              <InputError :message="form.errors.libelle" class="mt-2" />
            </div>

            <div>
              <InputLabel for="prix" value="Prix (FCFA) *" />
              <TextInput
                id="prix"
                v-model="form.prix"
                type="number"
                step="0.01"
                class="mt-1 block w-full"
                required
              />
              <InputError :message="form.errors.prix" class="mt-2" />
            </div>

            <div>
              <InputLabel for="quantite" value="Quantité *" />
              <TextInput
                id="quantite"
                v-model="form.quantite"
                type="number"
                class="mt-1 block w-full"
                required
              />
              <InputError :message="form.errors.quantite" class="mt-2" />
            </div>
          </div>

          <div class="mt-6 flex justify-end space-x-3">
            <SecondaryButton type="button" @click="closeModal">Annuler</SecondaryButton>
            <PrimaryButton type="submit" :disabled="form.processing">
              {{ showEditModal ? 'Mettre à jour' : 'Créer' }}
            </PrimaryButton>
          </div>
        </form>
      </div>
    </Modal>

    <!-- Update Stock Modal -->
    <Modal :show="showStockModal" @close="closeStockModal">
      <div class="p-6">
        <h3 class="text-lg font-semibold mb-4">Gérer le Stock</h3>

        <form @submit.prevent="updateStock()">
          <div class="space-y-4">
            <div>
              <p class="text-sm text-gray-600 mb-2">
                Article : <span class="font-semibold">{{ stockForm.article?.libelle }}</span>
              </p>
              <p class="text-sm text-gray-600 mb-4">
                Stock actuel : <span class="font-semibold">{{ stockForm.article?.quantite }}</span>
              </p>
            </div>

            <div>
              <InputLabel for="action" value="Action *" />
              <select
                id="action"
                v-model="stockForm.action"
                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/50 rounded-lg shadow-sm transition-all duration-200 hover:border-indigo-300"
                required
              >
                <option value="add">Ajouter au stock</option>
                <option value="set">Définir le stock</option>
              </select>
            </div>

            <div>
              <InputLabel for="stock_quantite" value="Quantité *" />
              <TextInput
                id="stock_quantite"
                v-model="stockForm.quantite"
                type="number"
                class="mt-1 block w-full"
                required
              />
              <InputError :message="stockForm.errors.quantite" class="mt-2" />
            </div>
          </div>

          <div class="mt-6 flex justify-end space-x-3">
            <SecondaryButton type="button" @click="closeStockModal">Annuler</SecondaryButton>
            <PrimaryButton type="submit" :disabled="stockForm.processing">
              Mettre à jour
            </PrimaryButton>
          </div>
        </form>
      </div>
    </Modal>

    <!-- Delete Confirmation Modal -->
    <Modal :show="showDeleteModal" @close="closeDeleteModal">
      <div class="p-6">
        <h3 class="text-lg font-semibold mb-4">Confirmer la suppression</h3>
        <p class="text-gray-600 mb-6">
          Êtes-vous sûr de vouloir supprimer l'article "<span class="font-semibold">{{ articleToDelete?.libelle }}</span>" ?
          Cette action est irréversible.
        </p>

        <div class="flex justify-end space-x-3">
          <SecondaryButton @click="closeDeleteModal">Annuler</SecondaryButton>
          <button
            @click="deleteArticle"
            class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
            :disabled="deleteForm.processing"
          >
            Supprimer
          </button>
        </div>
      </div>
    </Modal>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, reactive } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import Modal from '@/Components/Modal.vue';
import Pagination from '@/Components/Pagination.vue';

const props = defineProps({
  articles: Object,
  filters: Object,
});

const showCreateModal = ref(false);
const showEditModal = ref(false);
const showStockModal = ref(false);
const showDeleteModal = ref(false);
const articleToDelete = ref(null);

const form = useForm({
  libelle: '',
  prix: '',
  quantite: '',
});

const stockForm = useForm({
  article: null,
  quantite: '',
  action: 'add',
});

const deleteForm = useForm({});

const searchForm = reactive({
  search: props.filters?.search || '',
  disponible: props.filters?.disponible || '',
});

const formatMoney = (value) => {
  return new Intl.NumberFormat('fr-FR').format(value || 0);
};

const searchArticles = () => {
  router.get('/boutiquier/articles', searchForm, {
    preserveState: true,
    preserveScroll: true,
  });
};

const createArticle = () => {
  form.post('/boutiquier/articles', {
    preserveScroll: true,
    onSuccess: () => closeModal(),
  });
};

const openEditModal = (article) => {
  form.libelle = article.libelle;
  form.prix = article.prix;
  form.quantite = article.quantite;
  form.id = article.id;
  showEditModal.value = true;
};

const updateArticle = () => {
  form.put(`/boutiquier/articles/${form.id}`, {
    preserveScroll: true,
    onSuccess: () => closeModal(),
  });
};

const openUpdateStockModal = (article) => {
  stockForm.article = article;
  stockForm.quantite = '';
  stockForm.action = 'add';
  showStockModal.value = true;
};

const updateStock = () => {
  stockForm.post(`/boutiquier/articles/${stockForm.article.id}/stock`, {
    preserveScroll: true,
    onSuccess: () => closeStockModal(),
  });
};

const confirmDelete = (article) => {
  articleToDelete.value = article;
  showDeleteModal.value = true;
};

const deleteArticle = () => {
  deleteForm.delete(`/boutiquier/articles/${articleToDelete.value.id}`, {
    preserveScroll: true,
    onSuccess: () => closeDeleteModal(),
  });
};

const closeModal = () => {
  showCreateModal.value = false;
  showEditModal.value = false;
  form.reset();
  form.clearErrors();
};

const closeStockModal = () => {
  showStockModal.value = false;
  stockForm.reset();
  stockForm.clearErrors();
};

const closeDeleteModal = () => {
  showDeleteModal.value = false;
  articleToDelete.value = null;
};
</script>
