<template>
  <div class="min-h-screen bg-slate-50">
    <!-- Navbar -->
    <nav class="bg-white shadow">
      <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 justify-between">
          <div class="flex items-center">
            <div class="flex-shrink-0 flex items-center gap-2">
              <span
                class="text-xl font-bold text-indigo-600 bg-indigo-50 p-2 rounded-lg"
                >Poptin Polls</span
              >
            </div>
            <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
              <router-link
                to="/admin/dashboard"
                class="border-transparent text-slate-500 hover:border-slate-300 hover:text-slate-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium"
              >
                &larr; Back to Dashboard
              </router-link>
            </div>
          </div>
          <div class="flex items-center">
            <span class="text-sm text-slate-500 mr-4">Admin Portal</span>
          </div>
        </div>
      </div>
    </nav>

    <!-- Main Content -->
    <main
      v-if="loading"
      class="mx-auto max-w-5xl py-12 px-4 sm:px-6 lg:px-8 text-center text-slate-500"
    >
      Loading poll data...
    </main>

    <main
      v-else-if="error"
      class="mx-auto max-w-5xl py-12 px-4 sm:px-6 lg:px-8 text-center text-red-500"
    >
      {{ error }}
    </main>

    <main v-else-if="poll" class="mx-auto max-w-7xl py-8 sm:px-6 lg:px-8">
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Left Column: Poll Details & Results -->
        <div class="flex flex-col gap-8">
          <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6 flex justify-between items-center">
              <div>
                <h3 class="text-xl leading-6 font-bold text-slate-900">
                  {{ poll.question }}
                </h3>
                <p class="mt-1 max-w-2xl text-sm text-slate-500">
                  Created on
                  {{ new Date(poll.created_at).toLocaleDateString() }}
                </p>
              </div>
              <span
                v-if="poll.is_active"
                class="inline-flex items-center rounded-full bg-green-50 px-3 py-1 text-sm font-semibold text-green-700 ring-1 ring-inset ring-green-600/20"
                >Active</span
              >
              <span
                v-else
                class="inline-flex items-center rounded-full bg-yellow-50 px-3 py-1 text-sm font-semibold text-yellow-800 ring-1 ring-inset ring-yellow-600/20"
                >Paused</span
              >
            </div>
            <div class="border-t border-slate-200 px-4 py-5 sm:p-0">
              <dl class="sm:divide-y sm:divide-slate-200">
                <div
                  class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6"
                >
                  <dt class="text-sm font-medium text-slate-500">
                    Total Votes
                  </dt>
                  <dd
                    class="mt-1 text-sm font-bold text-indigo-600 sm:mt-0 sm:col-span-2"
                  >
                    {{ poll.votes?.length || 0 }}
                  </dd>
                </div>

                <div class="py-4 sm:py-5 sm:px-6">
                  <h4 class="text-sm font-medium text-slate-500 mb-4">
                    Results Distribution
                  </h4>
                  <div class="space-y-4">
                    <div v-for="option in poll.options" :key="option.id">
                      <div
                        class="flex justify-between text-sm font-medium mb-1"
                      >
                        <span class="text-slate-700">{{ option.text }}</span>
                        <span class="text-slate-600"
                          >{{ option.votes_count }} ({{
                            getPercentage(option.votes_count)
                          }}%)</span
                        >
                      </div>
                      <div class="w-full bg-slate-100 rounded-full h-2.5">
                        <div
                          class="bg-indigo-600 h-2.5 rounded-full transition-all duration-500"
                          :style="{
                            width: getPercentage(option.votes_count) + '%',
                          }"
                        ></div>
                      </div>
                    </div>
                  </div>
                </div>
              </dl>
            </div>
          </div>
        </div>

        <!-- Right Column: Detailed Voting Records -->
        <div
          class="bg-white shadow sm:rounded-lg h-fit max-h-[800px] flex flex-col"
        >
          <div class="px-4 py-5 border-b border-slate-200 sm:px-6 shrink-0">
            <h3 class="text-lg leading-6 font-medium text-slate-900">
              Detailed Voting Records
            </h3>
          </div>
          <div class="overflow-y-auto flex-1">
            <table
              v-if="poll.votes && poll.votes.length > 0"
              class="min-w-full divide-y divide-slate-200"
            >
              <thead class="bg-slate-50">
                <tr>
                  <th
                    scope="col"
                    class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider"
                  >
                    Voter
                  </th>
                  <th
                    scope="col"
                    class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider"
                  >
                    Option Chosen
                  </th>
                  <th
                    scope="col"
                    class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider"
                  >
                    Time
                  </th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-slate-200">
                <tr
                  v-for="vote in sortedVotes"
                  :key="vote.id"
                  class="hover:bg-slate-50"
                >
                  <td
                    class="px-6 py-4 whitespace-nowrap text-sm font-medium text-slate-900"
                  >
                    {{ vote.user ? vote.user.name : "Guest" }}
                    <span
                      v-if="vote.user"
                      class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-indigo-100 text-indigo-800"
                    >
                      Registered
                    </span>
                  </td>
                  <td
                    class="px-6 py-4 whitespace-nowrap text-sm text-slate-500"
                  >
                    {{
                      vote.option ? vote.option.text || "Unknown" : "Unknown"
                    }}
                  </td>
                  <td
                    class="px-6 py-4 whitespace-nowrap text-sm text-slate-500"
                  >
                    {{ new Date(vote.created_at).toLocaleString() }}
                  </td>
                </tr>
              </tbody>
            </table>
            <div v-else class="text-center py-8 text-slate-500 text-sm">
              No votes have been cast on this poll yet.
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, computed } from "vue";
import { useRoute, useRouter } from "vue-router";
import api from "../../services/api";

const route = useRoute();
const router = useRouter();
const poll = ref(null);
const loading = ref(true);
const error = ref("");

const totalVotes = computed(() => {
  if (!poll.value || !poll.value.options) return 0;
  return poll.value.options.reduce(
    (sum, opt) => sum + parseInt(opt.votes_count),
    0,
  );
});

const sortedVotes = computed(() => {
  if (!poll.value || !poll.value.votes) return [];
  return [...poll.value.votes].sort(
    (a, b) => new Date(b.created_at) - new Date(a.created_at),
  );
});

function getPercentage(votes) {
  if (totalVotes.value === 0) return 0;
  return Math.round((parseInt(votes) / totalVotes.value) * 100);
}

onMounted(async () => {
  try {
    const response = await api.get(`/polls/${route.params.id}/admin`);
    poll.value = response.data;
    listenForUpdates();
  } catch (e) {
    error.value = "Failed to load poll. It may have been deleted.";
  } finally {
    loading.value = false;
  }
});

onUnmounted(() => {
  if (poll.value) {
    window.Echo.leave(`poll.${poll.value.id}`);
  }
});

function listenForUpdates() {
  window.Echo.channel(`poll.${poll.value.id}`).listen(
    ".vote.registered",
    (e) => {
      // Refresh the entire poll data from API to get the populated user roles, OR just use the event data
      // For admin accuracy and simpler code, we might just re-fetch:
      refreshPoll();
    },
  );
}

async function refreshPoll() {
  try {
    const response = await api.get(`/polls/${route.params.id}/admin`);
    poll.value = response.data;
  } catch (e) {
    console.error("Failed to refresh poll data:", e);
  }
}
</script>
