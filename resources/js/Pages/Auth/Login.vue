<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
      <div>
        <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
          Gestion de Dettes
        </h2>
        <p class="mt-2 text-center text-sm text-gray-600">
          Connectez-vous à votre compte
        </p>
      </div>

      <form class="mt-8 space-y-6" @submit.prevent="submit">
        <div class="rounded-md shadow-sm -space-y-px">
          <div>
            <InputLabel for="login" value="Login" class="sr-only" />
            <TextInput
              id="login"
              v-model="form.login"
              type="text"
              class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-t-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
              placeholder="Login"
              required
              autofocus
            />
            <InputError class="mt-2" :message="form.errors.login" />
          </div>

          <div>
            <InputLabel for="password" value="Mot de passe" class="sr-only" />
            <TextInput
              id="password"
              v-model="form.password"
              type="password"
              class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-b-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
              placeholder="Mot de passe"
              required
            />
            <InputError class="mt-2" :message="form.errors.password" />
          </div>
        </div>

        <div v-if="form.errors.login || status" class="rounded-md bg-red-50 p-4">
          <div class="flex">
            <div class="ml-3">
              <h3 class="text-sm font-medium text-red-800">
                {{ status || 'Identifiants incorrects' }}
              </h3>
            </div>
          </div>
        </div>

        <div>
          <PrimaryButton
            type="submit"
            class="group relative w-full flex justify-center"
            :disabled="form.processing"
          >
            <span v-if="form.processing">Connexion en cours...</span>
            <span v-else>Se connecter</span>
          </PrimaryButton>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

defineProps({
  status: String,
});

const form = useForm({
  login: '',
  password: '',
});

const submit = () => {
  form.post('/login', {
    onFinish: () => form.reset('password'),
  });
};
</script>
