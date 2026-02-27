<template>
  <div class="min-h-screen bg-slate-50">
    <header class="bg-white shadow">
      <div
        class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex justify-between items-center"
      >
        <h1 class="text-2xl font-bold text-indigo-600">Poptin Polls</h1>
        <nav class="space-x-4">
          <template v-if="auth.isAuthenticated">
            <span class="text-slate-600 text-sm"
              >Welcome, {{ auth.user?.name }}</span
            >
            <router-link
              v-if="auth.user?.is_admin"
              to="/admin"
              class="text-indigo-600 hover:text-indigo-800 text-sm font-medium"
              >Dashboard</router-link
            >
            <button
              @click="handleLogout"
              class="text-red-500 hover:text-red-700 text-sm font-medium"
            >
              Logout
            </button>
          </template>
          <template v-else>
            <router-link
              to="/admin/login"
              class="text-indigo-600 hover:text-indigo-900 text-sm font-medium mr-4"
              >Admin Login</router-link
            >
            <router-link
              to="/login"
              class="text-slate-600 hover:text-slate-900 text-sm font-medium"
              >Login</router-link
            >
            <router-link
              to="/register"
              class="bg-indigo-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-indigo-700"
              >Register</router-link
            >
          </template>
        </nav>
      </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
      <div class="text-center mb-10">
        <h2
          class="text-4xl font-extrabold text-slate-900 tracking-tight sm:text-5xl"
        >
          Active Polls
        </h2>
        <p class="mt-4 text-lg text-slate-500">
          Have your say in the latest community questions.
        </p>
      </div>

      <div v-if="loading" class="text-center text-slate-500 py-10">
        Loading polls...
      </div>

      <div
        v-else-if="polls.length === 0"
        class="text-center text-slate-500 py-10"
      >
        No active polls available at the moment. Check back later!
      </div>

      <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div
          v-for="poll in polls"
          :key="poll.id"
          class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 flex flex-col justify-between hover:border-indigo-300 transition-colors"
        >
          <div>
            <h3 class="text-xl font-bold text-slate-900 mb-2">
              {{ poll.question }}
            </h3>
            <p class="text-sm text-slate-500 mb-4">
              {{ poll.options.length }} options participating
            </p>
          </div>

          <router-link
            :to="`/polls/${poll.slug}`"
            class="mt-4 flex justify-center w-full bg-indigo-50 text-indigo-700 hover:bg-indigo-100 py-2 border border-transparent rounded-md text-sm font-medium transition-colors"
          >
            View & Vote
          </router-link>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useRouter } from "vue-router";
import { useAuthStore } from "../stores/auth";
import api from "../services/api";

const auth = useAuthStore();
const router = useRouter();
const polls = ref([]);
const loading = ref(true);

const fetchPolls = async () => {
  try {
    const response = await api.get("/active-polls");
    polls.value = response.data;
  } catch (error) {
    console.error("Failed to fetch polls:", error);
  } finally {
    loading.value = false;
  }
};

const handleLogout = async () => {
  await auth.logout();
  router.go(); // Refresh the view to ensure state is clean
};

onMounted(() => {
  fetchPolls();
});
</script>
