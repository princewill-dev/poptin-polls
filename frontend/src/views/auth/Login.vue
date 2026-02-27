<template>
  <div
    class="flex min-h-full items-center justify-center py-12 px-4 sm:px-6 lg:px-8 bg-slate-50 min-h-screen"
  >
    <div
      class="w-full max-w-md space-y-8 bg-white p-8 rounded-xl shadow-sm border border-slate-200"
    >
      <div>
        <h2
          class="mt-6 text-center text-3xl font-bold tracking-tight text-slate-900"
        >
          Sign in to vote
        </h2>
        <p class="mt-2 text-center text-sm text-slate-600">
          Or
          <router-link
            to="/register"
            class="font-medium text-indigo-600 hover:text-indigo-500"
            >register a new account</router-link
          >
        </p>
      </div>
      <form class="mt-8 space-y-6" @submit.prevent="handleLogin">
        <div class="space-y-4">
          <div>
            <label
              for="email-address"
              class="block text-sm font-medium text-slate-700"
              >Email address</label
            >
            <input
              id="email-address"
              name="email"
              type="email"
              required
              v-model="form.email"
              class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-slate-900 placeholder-slate-400 focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm"
              placeholder="you@example.com"
            />
          </div>
          <div>
            <label
              for="password"
              class="block text-sm font-medium text-slate-700"
              >Password</label
            >
            <input
              id="password"
              name="password"
              type="password"
              required
              v-model="form.password"
              class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-slate-900 placeholder-slate-400 focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm"
              placeholder="••••••••"
            />
          </div>
        </div>

        <div
          v-if="error"
          class="text-red-500 text-sm p-3 bg-red-50 rounded-md border border-red-100 text-center font-medium"
        >
          {{ error }}
        </div>

        <div>
          <button
            type="submit"
            :disabled="loading"
            class="group relative flex w-full justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-50 transition-colors"
          >
            <span v-if="loading">Signing in...</span>
            <span v-else>Sign in</span>
          </button>
        </div>
      </form>
      <div class="text-center mt-4">
        <router-link to="/" class="text-sm text-slate-500 hover:text-slate-700"
          >&larr; Back to polls</router-link
        >
      </div>
    </div>
  </div>
</template>

<script setup>
import { reactive, ref } from "vue";
import { useAuthStore } from "../../stores/auth";
import { useRouter } from "vue-router";

const authStore = useAuthStore();
const router = useRouter();

const form = reactive({
  email: "",
  password: "",
});

const error = ref("");
const loading = ref(false);

async function handleLogin() {
  loading.value = true;
  error.value = "";

  try {
    await authStore.login(form);
    router.push({ name: "Home" });
  } catch (e) {
    error.value =
      e.response?.data?.message ||
      "Login failed. Please check your credentials.";
  } finally {
    loading.value = false;
  }
}
</script>
