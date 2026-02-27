<template>
  <div
    class="min-h-screen bg-slate-100 py-12 px-4 sm:px-6 lg:px-8 flex items-center justify-center"
  >
    <div v-if="loading" class="text-center text-slate-500 animate-pulse">
      Loading poll...
    </div>

    <div
      v-else-if="error"
      class="text-center text-red-500 font-medium bg-red-50 p-6 rounded-lg shadow-sm border border-red-100"
    >
      {{ error }}
    </div>

    <div class="max-w-xl w-full flex flex-col gap-6">
      <div v-if="poll">
        <router-link
          to="/"
          class="inline-flex items-center text-sm font-medium text-slate-500 hover:text-indigo-600 transition-colors bg-white px-4 py-2 rounded-lg shadow-sm w-fit group"
        >
          <svg
            class="w-4 h-4 mr-2 text-slate-400 group-hover:text-indigo-500 transition-colors"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
            xmlns="http://www.w3.org/2000/svg"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M10 19l-7-7m0 0l7-7m-7 7h18"
            ></path>
          </svg>
          Back to Home
        </router-link>
      </div>

      <div
        v-if="poll"
        class="bg-white rounded-2xl shadow-xl overflow-hidden border border-slate-100"
      >
        <div
          class="px-6 py-8 border-b border-slate-100 bg-gradient-to-r from-indigo-50 to-white"
        >
          <h2 class="text-2xl font-bold text-slate-900 text-center">
            {{ poll.question }}
          </h2>
        </div>

        <div class="p-6">
          <div class="space-y-4">
            <div
              v-for="option in poll.options"
              :key="option.id"
              class="relative group"
            >
              <!-- Option Content -->
              <div
                class="relative flex flex-col p-4 rounded-xl border-2 transition-all cursor-pointer overflow-hidden"
                :class="[
                  selectedOption === option.id
                    ? 'border-indigo-600 bg-indigo-50/50 shadow-md'
                    : 'border-slate-200 hover:border-indigo-300',
                  hasVoted
                    ? 'cursor-default border-slate-100 bg-white hover:border-slate-100'
                    : 'bg-white',
                ]"
                @click="!hasVoted && selectOption(option.id)"
              >
                <div class="flex items-center justify-between z-10 w-full mb-1">
                  <div class="flex items-center space-x-3">
                    <div
                      v-if="!hasVoted"
                      class="h-5 w-5 rounded-full border flex items-center justify-center shrink-0"
                      :class="
                        selectedOption === option.id
                          ? 'border-indigo-600 bg-indigo-600'
                          : 'border-slate-300'
                      "
                    >
                      <div
                        v-if="selectedOption === option.id"
                        class="h-2 w-2 rounded-full bg-white"
                      ></div>
                    </div>
                    <span
                      class="font-medium"
                      :class="
                        selectedOption === option.id
                          ? 'text-indigo-900 font-bold'
                          : 'text-slate-700'
                      "
                      >{{ option.text }}</span
                    >
                  </div>

                  <div
                    v-if="hasVoted"
                    class="font-bold text-slate-800 z-10 flex items-center gap-2 shrink-0"
                  >
                    <span class="text-indigo-600"
                      >{{ getPercentage(option.votes_count) }}%</span
                    >
                    <span
                      class="text-xs text-slate-500 font-normal hidden sm:inline-block"
                      >({{ option.votes_count }}
                      {{ option.votes_count === 1 ? "vote" : "votes" }})</span
                    >
                  </div>
                </div>

                <!-- Bar Chart Visual (Only visible after voting) -->
                <div
                  v-if="hasVoted"
                  class="w-full mt-3 h-2.5 bg-slate-100 rounded-full overflow-hidden flex z-10"
                >
                  <div
                    class="h-full rounded-full transition-all duration-1000 ease-out"
                    :class="
                      selectedOption === option.id
                        ? 'bg-indigo-600'
                        : 'bg-indigo-300'
                    "
                    :style="{ width: getPercentage(option.votes_count) + '%' }"
                  ></div>
                </div>
                <div
                  v-if="hasVoted"
                  class="text-[10px] text-slate-400 mt-1 sm:hidden text-right z-10"
                >
                  {{ option.votes_count }}
                  {{ option.votes_count === 1 ? "vote" : "votes" }}
                </div>
              </div>
            </div>
          </div>

          <!-- Submit Action -->
          <div class="mt-8 text-center">
            <button
              v-if="!hasVoted"
              @click="submitVote"
              :disabled="!selectedOption || voting"
              class="w-full sm:w-auto inline-flex justify-center rounded-xl bg-indigo-600 py-3 px-8 text-base font-medium text-white shadow-xl shadow-indigo-200 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed transition-all transform active:scale-95"
            >
              <svg
                v-if="voting"
                class="animate-spin -ml-1 mr-3 h-5 w-5 text-white"
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
              >
                <circle
                  class="opacity-25"
                  cx="12"
                  cy="12"
                  r="10"
                  stroke="currentColor"
                  stroke-width="4"
                ></circle>
                <path
                  class="opacity-75"
                  fill="currentColor"
                  d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                ></path>
              </svg>
              {{ voting ? "Submitting..." : "Submit Vote" }}
            </button>
            <div
              v-else
              class="text-sm font-medium text-slate-500 bg-slate-50 py-3 rounded-lg border border-slate-100 flex items-center justify-center gap-2"
            >
              <svg
                class="w-5 h-5 text-green-500"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M5 13l4 4L19 7"
                ></path>
              </svg>
              Thank you for voting. Results update in real-time.
            </div>
          </div>

          <!-- Live Indicator -->
          <div
            class="mt-6 flex items-center justify-center gap-2 text-xs text-slate-400 font-medium uppercase tracking-wider"
          >
            <span class="relative flex h-2 w-2">
              <span
                class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"
              ></span>
              <span
                class="relative inline-flex rounded-full h-2 w-2 bg-green-500"
              ></span>
            </span>
            Live Updates Enabled
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, computed } from "vue";
import { useRoute } from "vue-router";
import api, { csrf } from "../services/api";
import { getDeviceUUID } from "../utils/device";

const route = useRoute();
const poll = ref(null);
const loading = ref(true);
const error = ref("");
const selectedOption = ref(null);
const voting = ref(false);
const hasVoted = ref(false); // Can also be checked locally

const totalVotes = computed(() => {
  if (!poll.value || !poll.value.options) return 0;
  return poll.value.options.reduce(
    (sum, opt) => sum + parseInt(opt.votes_count),
    0,
  );
});

function getPercentage(votes) {
  if (totalVotes.value === 0) return 0;
  return Math.round((parseInt(votes) / totalVotes.value) * 100);
}

onMounted(async () => {
  const uuid = getDeviceUUID();

  try {
    const response = await api.get(
      `/polls/${route.params.id}?device_uuid=${uuid}`,
    );
    poll.value = response.data;

    // Prioritize backend state for hasVoted
    if (response.data.has_voted) {
      hasVoted.value = true;

      // Ensure localStorage is in sync if backend says we voted
      const votedPolls = JSON.parse(
        localStorage.getItem("voted_polls") || "[]",
      );
      if (!votedPolls.includes(route.params.id)) {
        votedPolls.push(route.params.id);
        localStorage.setItem("voted_polls", JSON.stringify(votedPolls));
      }
    } else {
      hasVoted.value = false;

      // If backend says we HAVEN'T voted, clear it from localStorage (e.g., after DB reset)
      const votedPolls = JSON.parse(
        localStorage.getItem("voted_polls") || "[]",
      );
      const index = votedPolls.indexOf(route.params.id);
      if (index > -1) {
        votedPolls.splice(index, 1);
        localStorage.setItem("voted_polls", JSON.stringify(votedPolls));
      }
    }

    listenForUpdates();
  } catch (e) {
    error.value = "Poll not found or inactive.";
  } finally {
    loading.value = false;
  }
});

onUnmounted(() => {
  if (poll.value) {
    window.Echo.leave(`poll.${poll.value.id}`);
  }
});

function selectOption(id) {
  if (hasVoted.value) return;
  selectedOption.value = id;
}

function listenForUpdates() {
  window.Echo.channel(`poll.${poll.value.id}`).listen(
    ".vote.registered",
    (e) => {
      // Update the poll with new data received via WebSockets
      poll.value = e.poll;
    },
  );
}

async function submitVote() {
  if (!selectedOption.value) return;

  voting.value = true;
  try {
    await csrf();
    const uuid = getDeviceUUID();
    await api.post(`/polls/${poll.value.id}/vote`, {
      option_id: selectedOption.value,
      device_uuid: uuid,
    });

    hasVoted.value = true;

    // Save to local storage to persist state across reloads
    const votedPolls = JSON.parse(localStorage.getItem("voted_polls") || "[]");
    votedPolls.push(route.params.id);
    localStorage.setItem("voted_polls", JSON.stringify(votedPolls));
  } catch (e) {
    error.value = e.response?.data?.message || "Error recording vote.";
    if (e.response?.status === 422 && e.response?.data?.errors?.device_uuid) {
      hasVoted.value = true; // Mark as voted if backend says they did
      alert("You have already voted on this poll.");
    }
  } finally {
    voting.value = false;
  }
}
</script>
