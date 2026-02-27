<template>
  <div
    class="flex min-h-screen items-center justify-center py-12 px-4 sm:px-6 lg:px-8 bg-slate-50"
  >
    <div
      v-if="checking"
      class="w-full max-w-md text-center text-slate-500 py-10"
    >
      Checking system status...
    </div>
    <div
      v-else-if="alreadyConfigured"
      class="w-full max-w-md space-y-8 bg-white p-8 rounded-xl shadow-lg border border-red-100 text-center"
    >
      <h2 class="text-2xl font-bold text-slate-900 mb-4">Setup Complete</h2>
      <p class="text-slate-600 mb-6">
        An admin account already exists. For security reasons, the initial setup
        is disabled.
      </p>
      <router-link
        to="/admin/login"
        class="inline-block bg-indigo-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-indigo-700"
      >
        Go to Admin Login
      </router-link>
    </div>
    <div
      v-else
      class="w-full max-w-md space-y-8 bg-white p-8 rounded-xl shadow-lg border border-slate-100"
    >
      <div>
        <h2
          class="mt-6 text-center text-3xl font-bold tracking-tight text-slate-900"
        >
          Initial Admin Setup
        </h2>
        <p class="mt-2 text-center text-sm text-slate-600">
          Create the master administrator account
        </p>
      </div>
      <form class="mt-8 space-y-6" @submit.prevent="handleSetup">
        <div class="space-y-4 rounded-md shadow-sm">
          <div>
            <label
              for="name"
              class="block text-sm font-medium text-slate-700 mb-1"
              >Full Name</label
            >
            <input
              id="name"
              name="name"
              type="text"
              required
              v-model="form.name"
              class="relative block w-full appearance-none rounded-md border border-slate-300 px-3 py-2 text-slate-900 placeholder-slate-500 focus:z-10 focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm"
              placeholder="Admin Name"
            />
          </div>
          <div>
            <label
              for="email-address"
              class="block text-sm font-medium text-slate-700 mb-1"
              >Email address</label
            >
            <input
              id="email-address"
              name="email"
              type="email"
              autocomplete="email"
              required
              v-model="form.email"
              class="relative block w-full appearance-none rounded-md border border-slate-300 px-3 py-2 text-slate-900 placeholder-slate-500 focus:z-10 focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm"
              placeholder="admin@example.com"
            />
          </div>
          <div>
            <label
              for="password"
              class="block text-sm font-medium text-slate-700 mb-1"
              >Password</label
            >
            <input
              id="password"
              name="password"
              type="password"
              autocomplete="new-password"
              required
              minlength="8"
              v-model="form.password"
              class="relative block w-full appearance-none rounded-md border border-slate-300 px-3 py-2 text-slate-900 placeholder-slate-500 focus:z-10 focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm"
              placeholder="••••••••"
            />
          </div>
          <div>
            <label
              for="password_confirmation"
              class="block text-sm font-medium text-slate-700 mb-1"
              >Confirm Password</label
            >
            <input
              id="password_confirmation"
              name="password_confirmation"
              type="password"
              autocomplete="new-password"
              required
              minlength="8"
              v-model="form.password_confirmation"
              class="relative block w-full appearance-none rounded-md border border-slate-300 px-3 py-2 text-slate-900 placeholder-slate-500 focus:z-10 focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm"
              placeholder="••••••••"
            />
          </div>
        </div>

        <div
          v-if="error"
          class="text-red-500 text-sm text-center font-medium bg-red-50 py-2 rounded"
        >
          {{ error }}
        </div>

        <div>
          <button
            type="submit"
            :disabled="loading"
            class="group relative flex w-full justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-50 transition-colors"
          >
            <span class="absolute inset-y-0 left-0 flex items-center pl-3">
              <svg
                class="h-5 w-5 text-indigo-500 group-hover:text-indigo-400"
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 20 20"
                fill="currentColor"
                aria-hidden="true"
              >
                <path
                  fill-rule="evenodd"
                  d="M10 1a4.5 4.5 0 00-4.5 4.5V9H5a2 2 0 00-2 2v6a2 2 0 002 2h10a2 2 0 002-2v-6a2 2 0 00-2-2h-.5V5.5A4.5 4.5 0 0010 1zm3 8V5.5a3 3 0 10-6 0V9h6z"
                  clip-rule="evenodd"
                />
              </svg>
            </span>
            {{ loading ? "Creating..." : "Create Admin Account" }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { reactive, ref, onMounted } from "vue";
import { useRouter } from "vue-router";
import api from "../../services/api";
import { useAuthStore } from "../../stores/auth";

const router = useRouter();
const authStore = useAuthStore();

const form = reactive({
  name: "",
  email: "",
  password: "",
  password_confirmation: "",
});

const checking = ref(true);
const alreadyConfigured = ref(false);
const error = ref("");
const loading = ref(false);

onMounted(async () => {
  try {
    const response = await api.get("/admin-exists");
    if (response.data.exists) {
      alreadyConfigured.value = true;
    }
  } catch (e) {
    error.value =
      "Failed to communicate with server. Please ensure backend is running.";
  } finally {
    checking.value = false;
  }
});

async function handleSetup() {
  if (form.password !== form.password_confirmation) {
    error.value = "Passwords do not match!";
    return;
  }

  loading.value = true;
  error.value = "";

  try {
    const response = await api.post("/admin-setup", form);

    // Auth is handled server side initially, let's fetch user into pinia
    await authStore.fetchUser(true);

    router.push({ name: "AdminDashboard" });
  } catch (e) {
    error.value =
      e.response?.data?.message || "Setup failed. Please check inputs.";
    if (e.response?.data?.errors) {
      const firstError = Object.values(e.response.data.errors)[0];
      error.value = Array.isArray(firstError) ? firstError[0] : firstError;
    }
  } finally {
    loading.value = false;
  }
}
</script>
