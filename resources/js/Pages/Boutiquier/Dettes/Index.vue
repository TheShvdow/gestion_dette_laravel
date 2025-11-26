<template>
  <AuthenticatedLayout :user="$page.props.auth.user">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">Gestion des Dettes</h2>
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
              <h3 class="text-lg font-semibold">Liste des Dettes</h3>
              <PrimaryButton @click="showCreateModal = true" class="w-full sm:w-auto justify-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Nouvelle Dette
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
                  class="pl-3 mt-1 block w-full h-10 border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/50 rounded-lg shadow-sm transition-all duration-200 hover:border-indigo-300"
                  placeholder="Rechercher par client..."
                  @input="searchDettes"
                />
              </div>
              <div>
                <InputLabel for="statut" value="Statut" />
                <select
                  id="statut"
                  v-model="searchForm.statut"
                  class=" pl-3 mt-1 block w-full h-10 border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/50 rounded-lg shadow-sm transition-all duration-200 hover:border-indigo-300"
                  @change="searchDettes"
                >
                  <option value="">Tous</option>
                  <option value="non_solde">Non Soldé</option>
                  <option value="solde">Soldé</option>
                </select>
              </div>
            </div>

            <!-- Dettes Table -->
            <div class="overflow-x-auto -mx-4 sm:mx-0">
              <div class="inline-block min-w-full align-middle">
                <div class="overflow-hidden">
                  <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gradient-to-r from-gray-50 to-slate-50">
                  <tr>
                    <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase whitespace-nowrap">Client</th>
                    <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase whitespace-nowrap">Date</th>
                    <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase whitespace-nowrap">Montant</th>
                    <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase whitespace-nowrap">Payé</th>
                    <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase whitespace-nowrap">Restant</th>
                    <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase whitespace-nowrap">Statut</th>
                    <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase whitespace-nowrap">Actions</th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr v-for="dette in dettes.data" :key="dette.id" class="hover:bg-gray-50 transition-colors">
                    <td class="px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap font-medium text-sm">{{ dette.client?.surname || 'N/A' }}</td>
                    <td class="px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-gray-600 text-sm">{{ formatDate(dette.date) }}</td>

                    <td class="px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap font-semibold text-sm">{{ formatMoney(dette.montant) }} FCFA</td>
                    <td class="px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-green-600 text-sm">{{ formatMoney(dette.montantDu) }} FCFA</td>
                    <td class="px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap">
                      <span :class="dette.montantRestant > 0 ? 'text-red-600 font-semibold text-sm' : 'text-green-600 font-semibold text-sm'">
                        {{ formatMoney(dette.montantRestant) }} FCFA
                      </span>
                    </td>
                    <td class="px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap">
                      <span :class="[
                        'px-2 py-1 text-xs font-semibold rounded-full whitespace-nowrap',
                        dette.status === 'Solde' ? 'bg-green-100 text-green-800' : 'bg-orange-100 text-orange-800'
                      ]">
                        {{ dette.status === 'Solde' ? 'Soldé' : 'Non Soldé' }}
                      </span>
                    </td>
                    <td class="px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-sm">
                      <!-- Desktop Actions -->
                      <div class="hidden sm:flex gap-2">
                        <Link :href="`/boutiquier/dettes/${dette.id}`" class="text-indigo-600 hover:text-indigo-900 font-medium">
                          Voir
                        </Link>
                        <button v-if="dette.status !== 'Solde'" @click="openPaiementModal(dette)" class="text-green-600 hover:text-green-900 font-medium">
                          Paiement
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
                            <DropdownLink :href="`/boutiquier/dettes/${dette.id}`">
                              <svg class="w-4 h-4 mr-2 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                              </svg>
                              Voir détails
                            </DropdownLink>
                            <button
                              v-if="dette.status !== 'Solde'"
                              @click="openPaiementModal(dette)"
                              class="w-full text-left block px-4 py-2 text-sm leading-5 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out"
                            >
                              <svg class="w-4 h-4 mr-2 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                              </svg>
                              Ajouter paiement
                            </button>
                          </template>
                        </Dropdown>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
                </div>
              </div>
            </div>

            <!-- Pagination -->
            <Pagination
              v-if="dettes.links"
              class="mt-4"
              :links="dettes.links"
              :from="dettes.from"
              :to="dettes.to"
              :total="dettes.total"
            />
          </div>
        </div>
      </div>
    </div>

    <!-- Create Dette Modal -->
    <Modal :show="showCreateModal" @close="closeCreateModal" max-width="4xl">
      <div class="p-4 sm:p-6 md:p-8">
        <h3 class="text-xl sm:text-2xl font-bold mb-4 sm:mb-6 bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
          Nouvelle Dette
        </h3>

        <form @submit.prevent="createDette">
          <div class="space-y-4 sm:space-y-6">
            <!-- Client Selection -->
            <div>
              <InputLabel for="client_id" value="Client *" class="text-sm sm:text-base mb-2" />
              <select
                id="client_id"
                v-model="detteForm.client_id"
                class="px-3 mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/50 rounded-lg shadow-sm transition-all duration-200 hover:border-indigo-300 text-sm sm:text-base py-2.5"
                required
              >
                <option value="">Sélectionner un client</option>
                <option v-for="client in clients" :key="client.id" :value="client.id">
                  {{ client.surname }} ({{ client.telephone }})
                </option>
              </select>
              <InputError :message="detteForm.errors.client_id" class="mt-2" />
            </div>

            <!-- Articles -->
            <div>
              <div class="flex justify-between items-center mb-3">
                <InputLabel value="Articles *" class="text-base" />
                <button
                  type="button"
                  @click="addArticleLine"
                  class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-indigo-600 hover:text-indigo-700 bg-indigo-50 hover:bg-indigo-100 rounded-lg transition-colors duration-200"
                >
                  <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                  </svg>
                  Ajouter un article
                </button>
              </div>

              <!-- Table Header - Hidden on mobile -->
              <div class="hidden md:grid grid-cols-12 gap-3 mb-2 px-2">
                <div class="col-span-5 text-xs font-medium text-gray-500 uppercase">Article</div>
                <div class="col-span-2 text-xs font-medium text-gray-500 uppercase">Quantité</div>
                <div class="col-span-3 text-xs font-medium text-gray-500 uppercase">Prix Unit.</div>
                <div class="col-span-2 text-xs font-medium text-gray-500 uppercase">Total</div>
              </div>

              <!-- Article Lines -->
              <div v-for="(line, index) in detteForm.articles" :key="index" class="grid grid-cols-1 md:grid-cols-12 gap-3 mb-3 bg-gray-50 p-3 rounded-lg hover:bg-gray-100 transition-colors duration-200">
                <div class="md:col-span-5">
                  <select
                    v-model="line.article_id"
                    @change="onArticleSelect(index)"
                    class="px-3 block w-full border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/50 rounded-lg shadow-sm text-sm transition-all duration-200 hover:border-indigo-300 py-2"
                    required
                  >
                    <option value="">Sélectionner un article</option>
                    <option
                      v-for="article in articles"
                      :key="article.id"
                      :value="article.id"
                      :class="getStockClass(article.quantite)"
                    >
                      {{ article.libelle }} -
                      <span v-if="article.quantite > 10">✓ {{ article.quantite }} en stock</span>
                      <span v-else-if="article.quantite > 0">⚠ {{ article.quantite }} restants</span>
                      <span v-else>✗ Rupture</span>
                    </option>
                  </select>
                  <!-- Stock Warning -->
                  <div v-if="line.article_id" class="mt-1 flex items-center text-xs">
                    <span v-if="getSelectedArticleStock(line.article_id) > 10" class="text-green-600 flex items-center">
                      <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                      </svg>
                      Stock disponible
                    </span>
                    <span v-else-if="getSelectedArticleStock(line.article_id) > 0" class="text-orange-600 flex items-center">
                      <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                      </svg>
                      Stock limité ({{ getSelectedArticleStock(line.article_id) }})
                    </span>
                  </div>
                </div>
                <div class="grid grid-cols-2 gap-2 md:col-span-2">
                  <label class="md:hidden text-xs font-medium text-gray-600">Quantité</label>
                  <input
                    v-model.number="line.quantite"
                    @input="calculateTotal"
                    type="number"
                    min="1"
                    :max="getSelectedArticleStock(line.article_id)"
                    placeholder="Qté"
                    class="pl-3 col-span-2 md:col-span-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/50 rounded-lg shadow-sm text-sm transition-all duration-200 hover:border-indigo-300 py-2"
                    required
                  />
                </div>
                <div class="grid grid-cols-2 gap-2 md:col-span-3">
                  <label class="md:hidden text-xs font-medium text-gray-600">Prix unitaire</label>
                  <input
                    v-model.number="line.prix"
                    @input="calculateTotal"
                    type="number"
                    step="0.01"
                    min="0"
                    placeholder="Prix"
                    class="pl-3 col-span-2 md:col-span-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/50 rounded-lg shadow-sm text-sm transition-all duration-200 hover:border-indigo-300 py-2"
                    required
                  />
                </div>
                <div class="md:col-span-2 flex items-center justify-between">
                  <div class="flex-1">
                    <span class="md:hidden text-xs font-medium text-gray-600">Total: </span>
                    <span class="text-base md:text-lg font-semibold text-indigo-600">{{ formatMoney(line.quantite * line.prix) }} FCFA</span>
                  </div>
                  <button
                    v-if="detteForm.articles.length > 1"
                    type="button"
                    @click="removeArticleLine(index)"
                    class="flex items-center justify-center text-red-500 hover:text-red-700 hover:bg-red-50 rounded-full p-1 transition-colors duration-200"
                    title="Supprimer cette ligne"
                  >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                  </button>
                </div>
              </div>

              <InputError :message="detteForm.errors.articles" class="mt-2" />
            </div>

            <!-- Total -->
            <div class="bg-gradient-to-r from-indigo-50 to-purple-50 p-4 sm:p-5 rounded-lg border-2 border-indigo-200">
              <div class="flex justify-between items-center gap-2">
                <span class="text-base sm:text-lg font-semibold text-gray-700">Montant Total:</span>
                <span class="text-xl sm:text-2xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                  {{ formatMoney(detteForm.montant) }} FCFA
                </span>
              </div>
            </div>
          </div>

          <div class="mt-6 sm:mt-8 flex flex-col sm:flex-row justify-end gap-3 sm:space-x-3">
            <SecondaryButton type="button" @click="closeCreateModal" class="w-full sm:w-auto justify-center">Annuler</SecondaryButton>
            <PrimaryButton type="submit" :disabled="detteForm.processing || detteForm.articles.length === 0" class="w-full sm:w-auto justify-center">
              Créer la Dette
            </PrimaryButton>
          </div>
        </form>
      </div>
    </Modal>

    <!-- Paiement Modal -->
    <Modal :show="showPaiementModal" @close="closePaiementModal" max-width="2xl">
      <div class="p-4 sm:p-6 md:p-8">
        <h3 class="text-xl sm:text-2xl font-bold mb-4 sm:mb-6 bg-gradient-to-r from-green-600 to-emerald-600 bg-clip-text text-transparent">
          Enregistrer un Paiement
        </h3>

        <form @submit.prevent="addPaiement">
          <div class="space-y-4 sm:space-y-6">
            <!-- Dette Info -->
            <div class="bg-gradient-to-r from-gray-50 to-slate-50 p-4 sm:p-5 rounded-lg border border-gray-200 space-y-2 sm:space-y-3">
              <div class="flex items-center justify-between gap-2">
                <span class="text-xs sm:text-sm font-medium text-gray-600">Client:</span>
                <span class="text-sm sm:text-base font-semibold text-gray-900 truncate">{{ paiementForm.dette?.client?.surname }}</span>
              </div>
              <div class="flex items-center justify-between gap-2">
                <span class="text-xs sm:text-sm font-medium text-gray-600">Montant total:</span>
                <span class="text-sm sm:text-base font-semibold text-gray-900">{{ formatMoney(paiementForm.dette?.montant) }} FCFA</span>
              </div>
              <div class="flex items-center justify-between gap-2">
                <span class="text-xs sm:text-sm font-medium text-gray-600">Déjà payé:</span>
                <span class="text-sm sm:text-base font-semibold text-green-600">{{ formatMoney(paiementForm.dette?.montantDu) }} FCFA</span>
              </div>
              <div class="border-t border-gray-300 pt-2 sm:pt-3 flex items-center justify-between gap-2">
                <span class="text-sm sm:text-base font-semibold text-gray-700">Reste à payer:</span>
                <span class="text-lg sm:text-xl font-bold text-red-600">{{ formatMoney(paiementForm.dette?.montantRestant) }} FCFA</span>
              </div>
            </div>

            <!-- Montant Input -->
            <div>
              <InputLabel for="montant_paiement" value="Montant du paiement (FCFA) *" class="text-sm sm:text-base mb-2" />
              <TextInput
                id="montant_paiement"
                v-model="paiementForm.montant"
                type="number"
                step="0.01"
                :max="paiementForm.dette?.montantRestant"
                class="mt-1 block w-full text-base sm:text-lg py-2.5 sm:py-3 px-3"
                placeholder="Entrez le montant à payer"
                required
              />
              <InputError :message="paiementForm.errors.montant" class="mt-2" />
              <p class="mt-2 text-xs text-gray-500">
                Maximum: {{ formatMoney(paiementForm.dette?.montantRestant) }} FCFA
              </p>
            </div>
          </div>

          <div class="mt-6 sm:mt-8 flex flex-col sm:flex-row justify-end gap-3 sm:space-x-3">
            <SecondaryButton type="button" @click="closePaiementModal" class="w-full sm:w-auto justify-center">Annuler</SecondaryButton>
            <PrimaryButton type="submit" :disabled="paiementForm.processing" class="w-full sm:w-auto justify-center">
              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              Enregistrer le Paiement
            </PrimaryButton>
          </div>
        </form>
      </div>
    </Modal>

    <!-- Delete Confirmation Modal -->
    <Modal :show="showDeleteModal" @close="closeDeleteModal" max-width="xl">
      <div class="p-8">
        <div class="flex items-center mb-6">
          <div class="flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
            <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
          </div>
          <h3 class="ml-4 text-xl font-bold text-gray-900">Confirmer la suppression</h3>
        </div>

        <p class="text-base text-gray-600 mb-6 leading-relaxed">
          Êtes-vous sûr de vouloir supprimer cette dette ? Cette action est <span class="font-semibold text-red-600">irréversible</span> et remettra automatiquement les articles en stock.
        </p>

        <div class="flex justify-end space-x-3 mt-8">
          <SecondaryButton type="button" @click="closeDeleteModal">Annuler</SecondaryButton>
          <button
            @click="deleteDette"
            class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-lg font-semibold text-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transform hover:scale-105 transition-all duration-150 shadow-md hover:shadow-lg disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none"
            :disabled="deleteForm.processing"
          >
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
            </svg>
            Supprimer définitivement
          </button>
        </div>
      </div>
    </Modal>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, reactive, watch } from 'vue';
import { Link, useForm, router, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import Modal from '@/Components/Modal.vue';
import Pagination from '@/Components/Pagination.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import { useToast } from '@/Composables/useToast';

const page = usePage();

const props = defineProps({
  dettes: Object,
  clients: Array,
  articles: Array,
  filters: Object,
});

const showCreateModal = ref(false);
const showPaiementModal = ref(false);
const showDeleteModal = ref(false);
const detteToDelete = ref(null);

const detteForm = useForm({
  client_id: '',
  montant: 0,
  articles: [{ article_id: '', quantite: 1, prix: 0 }],
});

const paiementForm = useForm({
  dette: null,
  montant: '',
});

const deleteForm = useForm({});

const searchForm = reactive({
  search: props.filters?.search || '',
  statut: props.filters?.statut || '',
});

const formatMoney = (value) => {
  return new Intl.NumberFormat('fr-FR').format(value || 0);
};

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('fr-FR');
};

const searchDettes = () => {
  router.get('/boutiquier/dettes', searchForm, {
    preserveState: true,
    preserveScroll: true,
  });
};

const addArticleLine = () => {
  detteForm.articles.push({ article_id: '', quantite: 1, prix: 0 });
};

const removeArticleLine = (index) => {
  detteForm.articles.splice(index, 1);
  calculateTotal();
};

const onArticleSelect = (index) => {
  const line = detteForm.articles[index];
  const article = props.articles.find(a => a.id === line.article_id);
  if (article) {
    line.prix = article.prix;
    calculateTotal();
  }
};

const calculateTotal = () => {
  detteForm.montant = detteForm.articles.reduce((total, line) => {
    return total + (line.quantite * line.prix);
  }, 0);
};

const createDette = () => {
  detteForm.post('/boutiquier/dettes', {
    preserveScroll: true,
    onSuccess: () => {
      closeCreateModal();
      const toast = useToast();
      toast.success('Dette créée !', `La dette de ${formatMoney(detteForm.montant)} FCFA a été créée avec succès.`);
    },
    onError: () => {
      const toast = useToast();
      toast.error('Erreur', 'Une erreur est survenue lors de la création de la dette.');
    },
  });
};

const openPaiementModal = (dette) => {
  paiementForm.dette = dette;
  paiementForm.montant = '';
  showPaiementModal.value = true;
};

const addPaiement = () => {
  const montant = paiementForm.montant;
  paiementForm.post(`/boutiquier/dettes/${paiementForm.dette.id}/paiement`, {
    only: ['montant'],
    preserveScroll: false,
    onSuccess: () => {
      closePaiementModal();
      router.reload({ only: ['dettes'] });
      const toast = useToast();
      toast.success('Paiement enregistré !', `Un paiement de ${formatMoney(montant)} FCFA a été enregistré avec succès.`);
    },
    onError: () => {
      const toast = useToast();
      toast.error('Erreur', 'Une erreur est survenue lors de l\'enregistrement du paiement.');
    },
  });
};

const confirmDelete = (dette) => {
  detteToDelete.value = dette;
  showDeleteModal.value = true;
};

const deleteDette = () => {
  deleteForm.delete(`/boutiquier/dettes/${detteToDelete.value.id}`, {
    preserveScroll: true,
    onSuccess: () => {
      closeDeleteModal();
      const toast = useToast();
      toast.success('Dette supprimée !', 'La dette a été supprimée avec succès et les articles remis en stock.');
    },
    onError: () => {
      const toast = useToast();
      toast.error('Erreur', 'Une erreur est survenue lors de la suppression de la dette.');
    },
  });
};

const closeCreateModal = () => {
  showCreateModal.value = false;
  detteForm.reset();
  detteForm.articles = [{ article_id: '', quantite: 1, prix: 0 }];
  detteForm.clearErrors();
};

const closePaiementModal = () => {
  showPaiementModal.value = false;
  paiementForm.reset();
  paiementForm.clearErrors();
};

const closeDeleteModal = () => {
  showDeleteModal.value = false;
  detteToDelete.value = null;
};

// Stock management functions
const getSelectedArticleStock = (articleId) => {
  if (!articleId) return 0;
  const article = props.articles.find(a => a.id === articleId);
  return article ? article.quantite : 0;
};

const getStockClass = (quantite) => {
  if (quantite > 10) {
    return 'text-green-700';
  } else if (quantite > 0) {
    return 'text-orange-600';
  }
  return 'text-red-600';
};
</script>
