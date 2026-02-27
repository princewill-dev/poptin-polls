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
          </div>
          <div class="flex items-center">
            <span class="text-sm text-slate-500 mr-4"
              >Hello, {{ authStore.user?.name }}</span
            >
            <button
              @click="logout"
              class="text-sm font-medium text-slate-500 hover:text-slate-700"
            >
              Logout
            </button>
          </div>
        </div>
      </div>
    </nav>

    <!-- Main Content -->
    <main>
      <div class="mx-auto max-w-7xl py-6 sm:px-6 lg:px-8">
        <!-- Header actions -->
        <div class="flex items-center justify-between mb-8">
          <h1 class="text-2xl font-extrabold tracking-tight text-slate-900">
            Dashboard
          </h1>
          <button
            @click="showModal = true"
            class="inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700"
          >
            + Create New Poll
          </button>
        </div>

        <!-- Create Poll Modal -->
        <div
          v-if="showModal"
          class="fixed inset-0 z-50 overflow-y-auto"
          aria-labelledby="modal-title"
          role="dialog"
          aria-modal="true"
        >
          <div
            class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0"
          >
            <!-- Background overlay -->
            <div
              class="fixed inset-0 bg-slate-500 bg-opacity-75 transition-opacity"
              @click="showModal = false"
              aria-hidden="true"
            ></div>

            <span
              class="hidden sm:inline-block sm:align-middle sm:h-screen"
              aria-hidden="true"
              >&#8203;</span
            >

            <!-- Modal panel -->
            <div
              class="relative inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full"
            >
              <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                  <div
                    class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full"
                  >
                    <h3
                      class="text-lg leading-6 font-medium text-slate-900 mb-4"
                      id="modal-title"
                    >
                      Create a New Poll
                    </h3>
                    <form @submit.prevent="createPoll" class="space-y-4 w-full">
                      <div>
                        <label class="block text-sm font-medium text-slate-700"
                          >Question</label
                        >
                        <input
                          type="text"
                          v-model="newPoll.question"
                          required
                          class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2 border"
                          placeholder="What's your favorite framework?"
                        />
                      </div>

                      <div>
                        <label
                          class="block text-sm font-medium text-slate-700 mb-2"
                          >Options</label
                        >
                        <div
                          v-for="(option, index) in newPoll.options"
                          :key="index"
                          class="flex gap-2 mb-2"
                        >
                          <input
                            type="text"
                            v-model="newPoll.options[index]"
                            required
                            class="block w-full rounded-md border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2 border"
                            :placeholder="`Option ${index + 1}`"
                          />
                          <button
                            type="button"
                            v-if="newPoll.options.length > 2"
                            @click="removeOption(index)"
                            class="text-red-600 hover:text-red-800 px-2 font-bold"
                          >
                            &times;
                          </button>
                        </div>
                        <button
                          type="button"
                          @click="addOption"
                          class="mt-2 text-sm text-indigo-600 hover:text-indigo-800 font-medium"
                        >
                          + Add another option
                        </button>
                      </div>

                      <div class="pt-4 flex gap-3 justify-end">
                        <button
                          type="button"
                          @click="showModal = false"
                          class="inline-flex justify-center rounded-md border border-slate-300 bg-white py-2 px-4 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                        >
                          Cancel
                        </button>
                        <button
                          type="submit"
                          :disabled="loading"
                          class="inline-flex justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                        >
                          {{ loading ? "Saving..." : "Create Poll" }}
                        </button>
                      </div>
                    </form>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Your Polls Grid -->
        <div class="mb-6">
          <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
            <h3 class="text-lg font-medium leading-6 text-slate-900">
              Your Polls
            </h3>
          </div>
          <div class="p-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <div
              v-for="poll in polls"
              :key="poll.id"
              class="bg-white rounded-xl shadow-sm border border-slate-200 hover:shadow-md transition-shadow overflow-hidden flex flex-col"
            >
              <div class="p-5 flex-1 flex flex-col">
                <div class="flex items-start justify-between gap-4 mb-3">
                  <h4 class="text-lg font-bold text-slate-800">
                    {{ poll.question }}
                  </h4>
                  <span
                    v-if="poll.is_active"
                    class="shrink-0 inline-flex items-center rounded-full bg-green-50 px-2 py-1 text-xs font-semibold text-green-700 ring-1 ring-inset ring-green-600/20"
                    >Active</span
                  >
                  <span
                    v-else
                    class="shrink-0 inline-flex items-center rounded-full bg-yellow-50 px-2 py-1 text-xs font-semibold text-yellow-800 ring-1 ring-inset ring-yellow-600/20"
                    >Paused</span
                  >
                </div>

                <div
                  class="flex items-center gap-4 text-sm text-slate-500 mb-6 flex-1"
                >
                  <div class="flex items-center gap-1">
                    <svg
                      class="w-4 h-4 text-slate-400"
                      fill="none"
                      stroke="currentColor"
                      viewBox="0 0 24 24"
                    >
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"
                      ></path>
                    </svg>
                    <span class="font-medium">{{
                      poll.votes?.length || 0
                    }}</span>
                    total votes
                  </div>
                  <div class="flex items-center gap-1">
                    <svg
                      class="w-4 h-4 text-slate-400"
                      fill="none"
                      stroke="currentColor"
                      viewBox="0 0 24 24"
                    >
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                      ></path>
                    </svg>
                    <span>{{
                      new Date(poll.created_at).toLocaleDateString()
                    }}</span>
                  </div>
                </div>

                <!-- Admin Actions -->
                <div
                  class="flex flex-wrap items-center gap-2 pt-4 border-t border-slate-100"
                >
                  <router-link
                    :to="{ name: 'AdminPollView', params: { id: poll.id } }"
                    class="flex-1 text-center rounded-lg bg-indigo-50 px-3 py-2 text-sm font-semibold text-indigo-700 hover:bg-indigo-100 transition-colors"
                  >
                    View Poll
                  </router-link>
                  <button
                    @click="toggleStatus(poll)"
                    class="rounded-lg bg-slate-100 p-2 text-slate-600 hover:bg-slate-200 hover:text-slate-900 transition-colors"
                    :title="poll.is_active ? 'Pause Poll' : 'Resume Poll'"
                  >
                    <svg
                      v-if="poll.is_active"
                      class="w-5 h-5"
                      fill="none"
                      stroke="currentColor"
                      viewBox="0 0 24 24"
                    >
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"
                      ></path>
                    </svg>
                    <svg
                      v-else
                      class="w-5 h-5"
                      fill="none"
                      stroke="currentColor"
                      viewBox="0 0 24 24"
                    >
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"
                      ></path>
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                      ></path>
                    </svg>
                  </button>
                  <button
                    @click="editPoll(poll)"
                    class="rounded-lg bg-blue-50 p-2 text-blue-600 hover:bg-blue-100 hover:text-blue-800 transition-colors"
                    title="Edit Poll"
                  >
                    <svg
                      class="w-5 h-5"
                      fill="none"
                      stroke="currentColor"
                      viewBox="0 0 24 24"
                    >
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"
                      ></path>
                    </svg>
                  </button>
                  <button
                    @click="deletePoll(poll.id)"
                    class="rounded-lg bg-red-50 p-2 text-red-600 hover:bg-red-100 hover:text-red-800 transition-colors"
                    title="Delete Poll"
                  >
                    <svg
                      class="w-5 h-5"
                      fill="none"
                      stroke="currentColor"
                      viewBox="0 0 24 24"
                    >
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                      ></path>
                    </svg>
                  </button>
                </div>
              </div>

              <!-- Voting Records Accordion/Details -->
              <div class="border-t border-slate-100 bg-slate-50">
                <details class="group">
                  <summary
                    class="px-5 py-3 text-sm font-semibold text-slate-700 cursor-pointer hover:bg-slate-100 list-none flex items-center justify-between"
                  >
                    <span>Recent Votes</span>
                    <svg
                      class="w-4 h-4 text-slate-400 group-open:rotate-180 transition-transform"
                      fill="none"
                      stroke="currentColor"
                      viewBox="0 0 24 24"
                    >
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M19 9l-7 7-7-7"
                      ></path>
                    </svg>
                  </summary>
                  <div class="px-5 pb-4 pt-1 max-h-48 overflow-y-auto">
                    <div
                      v-if="poll.votes && poll.votes.length > 0"
                      class="space-y-2"
                    >
                      <div
                        v-for="vote in poll.votes"
                        :key="vote.id"
                        class="flex flex-col text-xs bg-white p-2 rounded border border-slate-100"
                      >
                        <div class="flex justify-between items-center mb-1">
                          <span class="font-bold text-slate-700">
                            {{ vote.user ? vote.user.name : "Guest" }}
                            <span
                              v-if="vote.user"
                              class="text-[10px] text-indigo-600 bg-indigo-50 px-1 rounded ml-1"
                              >Registered</span
                            >
                          </span>
                          <span class="text-slate-400">{{
                            new Date(vote.created_at).toLocaleTimeString()
                          }}</span>
                        </div>
                        <span class="text-slate-600 truncate"
                          >Voted:
                          <span class="font-medium">{{
                            vote.option
                              ? vote.option.text || "Unknown"
                              : "Unknown"
                          }}</span></span
                        >
                      </div>
                    </div>
                    <div
                      v-else
                      class="text-xs text-slate-500 italic text-center py-2"
                    >
                      No votes yet.
                    </div>
                  </div>
                </details>
              </div>
            </div>

            <div
              v-if="polls.length === 0"
              class="col-span-full py-12 text-center text-slate-500 text-sm bg-white rounded-xl shadow-sm border border-slate-200"
            >
              No polls created yet. Click "Create New Poll" to get started.
            </div>
          </div>
        </div>
      </div>
    </main>

    <!-- Edit Poll Modal -->
    <div
      v-if="showEditModal"
      class="fixed inset-0 z-50 overflow-y-auto"
      aria-labelledby="modal-title"
      role="dialog"
      aria-modal="true"
    >
      <div
        class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0"
      >
        <div
          class="fixed inset-0 bg-slate-500 bg-opacity-75 transition-opacity"
          aria-hidden="true"
          @click="showEditModal = false"
        ></div>
        <span
          class="hidden sm:inline-block sm:align-middle sm:h-screen"
          aria-hidden="true"
          >&#8203;</span
        >
        <div
          class="relative inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6"
        >
          <div>
            <div class="mt-3 text-center sm:mt-5">
              <h3
                class="text-lg leading-6 font-medium text-slate-900"
                id="modal-title"
              >
                Edit Poll
              </h3>
              <div class="mt-4 text-left">
                <form @submit.prevent="submitEditPoll">
                  <div class="space-y-4">
                    <div>
                      <label
                        class="block text-sm font-medium text-slate-700 mb-1"
                        >Question</label
                      >
                      <input
                        type="text"
                        v-model="editPollData.question"
                        required
                        class="block w-full rounded-md border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2 border"
                      />
                    </div>

                    <div>
                      <label
                        class="block text-sm font-medium text-slate-700 mb-2"
                        >Options</label
                      >
                      <div
                        v-for="(option, index) in editPollData.options"
                        :key="index"
                        class="flex gap-2 mb-2"
                      >
                        <input
                          type="text"
                          v-model="editPollData.options[index].text"
                          required
                          class="block w-full rounded-md border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2 border"
                          :placeholder="`Option ${index + 1}`"
                        />
                        <button
                          type="button"
                          v-if="editPollData.options.length > 2"
                          @click="removeEditOption(index)"
                          class="text-red-600 hover:text-red-800 px-2 font-bold"
                        >
                          &times;
                        </button>
                      </div>
                      <button
                        type="button"
                        @click="addEditOption"
                        class="mt-2 text-sm text-indigo-600 hover:text-indigo-800 font-medium"
                      >
                        + Add another option
                      </button>
                    </div>

                    <div class="pt-4 flex gap-3 justify-end">
                      <button
                        type="button"
                        @click="showEditModal = false"
                        class="inline-flex justify-center rounded-md border border-slate-300 bg-white py-2 px-4 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                      >
                        Cancel
                      </button>
                      <button
                        type="submit"
                        :disabled="loading"
                        class="inline-flex justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                      >
                        {{ loading ? "Saving..." : "Save Changes" }}
                      </button>
                    </div>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useAuthStore } from "../../stores/auth";
import { useRouter } from "vue-router";
import api from "../../services/api";

const authStore = useAuthStore();
const router = useRouter();

const polls = ref([]);
const loading = ref(false);
const showModal = ref(false);
const showEditModal = ref(false);

const newPoll = ref({
  question: "",
  options: ["", ""],
});

const editPollData = ref({
  id: null,
  question: "",
  options: [],
});

onMounted(() => {
  fetchPolls();
});

async function logout() {
  await authStore.logout();
  router.push({ name: "AdminLogin" });
}

async function fetchPolls() {
  try {
    const response = await api.get("/polls");
    polls.value = response.data;
    attachListeners();
  } catch (e) {
    console.error("Failed to fetch polls");
  }
}

function attachListeners() {
  polls.value.forEach((poll) => {
    window.Echo.channel(`poll.${poll.id}`).listen(".vote.registered", (e) => {
      const idx = polls.value.findIndex((p) => p.id === poll.id);
      if (idx !== -1) {
        // Because the event returns the updated poll, we just overwrite it
        // Note: Make sure the event publishes the fully loaded relationships in RegisterVoteAction
        // Since we did not update RegisterVoteAction to load `votes.user` deeply, we might need a manual refresh
        // For now, let's just re-fetch the entire list for simplicity and 100% correctness on the admin side
        fetchPolls();
      }
    });
  });
}

async function toggleStatus(poll) {
  try {
    const res = await api.patch(`/polls/${poll.id}/toggle-status`);
    const idx = polls.value.findIndex((p) => p.id === poll.id);
    if (idx !== -1) polls.value[idx] = res.data;
  } catch (e) {
    alert("Failed to toggle status");
  }
}

async function deletePoll(pollId) {
  if (
    !confirm(
      "Are you sure you want to delete this poll? This cannot be undone.",
    )
  )
    return;
  try {
    await api.delete(`/polls/${pollId}`);
    polls.value = polls.value.filter((p) => p.id !== pollId);
  } catch (e) {
    alert("Failed to delete poll");
  }
}

function editPoll(poll) {
  editPollData.value.id = poll.id;
  editPollData.value.question = poll.question;
  editPollData.value.options = poll.options.map((o) => ({
    id: o.id,
    text: o.text,
  }));
  showEditModal.value = true;
}

async function submitEditPoll() {
  if (
    editPollData.value.question.trim() === "" ||
    editPollData.value.options.some((o) => o.text.trim() === "")
  ) {
    alert("Please fill in all fields.");
    return;
  }

  loading.value = true;
  try {
    const res = await api.put(`/polls/${editPollData.value.id}`, {
      question: editPollData.value.question,
      options: editPollData.value.options,
    });
    const idx = polls.value.findIndex((p) => p.id === editPollData.value.id);
    if (idx !== -1) polls.value[idx] = res.data;
    showEditModal.value = false;
  } catch (e) {
    alert("Failed to edit poll");
  } finally {
    loading.value = false;
  }
}

function addEditOption() {
  editPollData.value.options.push({ text: "" });
}

function removeEditOption(index) {
  if (editPollData.value.options.length > 2) {
    editPollData.value.options.splice(index, 1);
  }
}

function addOption() {
  newPoll.value.options.push("");
}

function removeOption(index) {
  if (newPoll.value.options.length > 2) {
    newPoll.value.options.splice(index, 1);
  }
}

async function createPoll() {
  if (
    newPoll.value.question.trim() === "" ||
    newPoll.value.options.some((o) => o.trim() === "")
  ) {
    alert("Please fill in all fields.");
    return;
  }

  loading.value = true;
  try {
    const response = await api.post("/polls", newPoll.value);
    // After creating a poll, re-fetch to get all relationships structured uniformly
    await fetchPolls();
    newPoll.value = { question: "", options: ["", ""] };
    showModal.value = false;
  } catch (e) {
    alert(e.response?.data?.message || "Error creating poll");
  } finally {
    loading.value = false;
  }
}
</script>
