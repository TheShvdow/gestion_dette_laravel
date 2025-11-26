<template>
  <AuthenticatedLayout :user="$page.props.auth.user">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">Gestion des Articles</h2>
    </template>

    <div class="py-6 sm:py-12">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Success/Error Messages -->
        <div v-if="$page.props.flash.success" class="mb-4 bg-gradient-to-r from-green-50 to-emerald-50 border-l-4 border-green-500 text-green-700 px-4 sm:px-6 py-3 sm:py-4 rounded-lg shadow-md animate-slideIn flex items-center text-sm sm:text-base">
          <svg class="w-5 h-5 mr-3 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          {{ $page.props.flash.success }}
        </div>
        <div v-if="$page.props.flash.error" class="mb-4 bg-gradient-to-r from-red-50 to-rose-50 border-l-4 border-red-500 text-red-700 px-4 sm:px-6 py-3 sm:py-4 rounded-lg shadow-md animate-slideIn flex items-center text-sm sm:text-base">
          <svg class="w-5 h-5 mr-3 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          {{ $page.props.flash.error }}
        </div>

        <div class="bg-white/80 backdrop-blur-sm overflow-hidden shadow-lg sm:rounded-xl border border-gray-200 hover:shadow-xl transition-shadow duration-300">
          <div class="p-4 sm:p-6">
            <!-- Header with Actions -->
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 sm:gap-0 mb-6">
              <h3 class="text-lg font-semibold">Liste des Articles</h3>
              <PrimaryButton @click="showCreateModal = true" class="w-full sm:w-auto justify-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
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
                  class="pl-3 h-10 mt-1 block w-full"
                  placeholder="Rechercher par libellé..."
                  @input="searchArticles"
                />
              </div>
              <div>
                <InputLabel for="disponible" value="Disponibilité" />
                <select
                  id="disponible"
                  v-model="searchForm.disponible"
                  class="h-10 pl-3 mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/50 rounded-lg shadow-sm transition-all duration-200 hover:border-indigo-300"
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
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                      <!-- Desktop Actions -->
                      <div class="hidden sm:flex gap-2">
                        <button @click="openUpdateStockModal(article)" class="text-blue-600 hover:text-blue-900 font-medium">
                          Stock
                        </button>
                        <button @click="openEditModal(article)" class="text-indigo-600 hover:text-indigo-900 font-medium">
                          Modifier
                        </button>
                        <button @click="confirmDelete(article)" class="text-red-600 hover:text-red-900 font-medium">
                          Supprimer
                        </button>
                      </div>

                      <!-- Mobile Actions - Dropdown -->
                      <div class="sm:hidden">
                        <Dropdown align="right" width="48">
                          <template #trigger>
                            <button class="p-2 rounded-lg hover:bg-gray-100 transition-colors">
                              <svg class="w-5 h-5 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                              </svg>
                            </button>
                          </template>
                          <template #content>
                            <button
                              @click="openUpdateStockModal(article)"
                              class="w-full text-left block px-4 py-2 text-sm leading-5 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out"
                            >
                              <svg class="w-4 h-4 mr-2 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                              </svg>
                              Gérer le stock
                            </button>
                            <button
                              @click="openEditModal(article)"
                              class="w-full text-left block px-4 py-2 text-sm leading-5 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out"
                            >
                              <svg class="w-4 h-4 mr-2 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                              </svg>
                              Modifier
                            </button>
                            <button
                              @click="confirmDelete(article)"
                              class="w-full text-left block px-4 py-2 text-sm leading-5 text-red-600 hover:bg-red-50 focus:outline-none focus:bg-red-50 transition duration-150 ease-in-out"
                            >
                              <svg class="w-4 h-4 mr-2 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                              </svg>
                              Supprimer
                            </button>
                          </template>
                        </Dropdown>
                      </div>
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
                class="h-10 p-3 mt-1 block w-full"
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
                class="h-10 p-3 mt-1 block w-full"
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
                class="mt-1 block w-full h-10 p-3"
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
                class="h-10 p-3 mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/50 rounded-lg shadow-sm transition-all duration-200 hover:border-indigo-300"
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
                class="mt-1 block w-full h-10 p-3"
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
import Dropdown from '@/Components/Dropdown.vue';
import Modal from '@/Components/Modal.vue';
import Pagination from '@/Components/Pagination.vue';
import { useToast } from '@/Composables/useToast';

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
    onSuccess: () => {
      closeModal();
      const toast = useToast();
      toast.success('Article créé !', `L'article "${form.libelle}" a été créé avec succès.`);
    },
    onError: () => {
      const toast = useToast();
      toast.error('Erreur', 'Une erreur est survenue lors de la création de l\'article.');
    },
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
    onSuccess: () => {
      closeModal();
      const toast = useToast();
      toast.success('Article modifié !', `L'article "${form.libelle}" a été mis à jour avec succès.`);
    },
    onError: () => {
      const toast = useToast();
      toast.error('Erreur', 'Une erreur est survenue lors de la modification de l\'article.');
    },
  });
};

const openUpdateStockModal = (article) => {
  stockForm.article = article;
  stockForm.quantite = '';
  stockForm.action = 'add';
  showStockModal.value = true;
};

const updateStock = () => {
  const article = stockForm.article;
  const quantite = stockForm.quantite;
  const action = stockForm.action;

  stockForm.post(`/boutiquier/articles/${stockForm.article.id}/stock`, {
    preserveScroll: true,
    onSuccess: () => {
      closeStockModal();
      const toast = useToast();
      if (action === 'add') {
        toast.success('Réapprovisionnement réussi !', `${quantite} unités ont été ajoutées au stock de "${article.libelle}".`);
      } else {
        toast.success('Stock mis à jour !', `${quantite} unités ont été retirées du stock de "${article.libelle}".`);
      }
    },
    onError: () => {
      const toast = useToast();
      toast.error('Erreur', 'Une erreur est survenue lors de la mise à jour du stock.');
    },
  });
};

const confirmDelete = (article) => {
  articleToDelete.value = article;
  showDeleteModal.value = true;
};

const deleteArticle = () => {
  const articleName = articleToDelete.value.libelle;
  deleteForm.delete(`/boutiquier/articles/${articleToDelete.value.id}`, {
    preserveScroll: true,
    onSuccess: () => {
      closeDeleteModal();
      const toast = useToast();
      toast.success('Article supprimé !', `L'article "${articleName}" a été supprimé avec succès.`);
    },
    onError: () => {
      const toast = useToast();
      toast.error('Erreur', 'Une erreur est survenue lors de la suppression de l\'article.');
    },
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
