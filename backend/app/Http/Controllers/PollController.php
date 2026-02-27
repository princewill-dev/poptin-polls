<?php

namespace App\Http\Controllers;

use App\Actions\CreatePollAction;
use App\Http\Requests\StorePollRequest;
use App\Models\Poll;
use Illuminate\Http\Request;

class PollController extends Controller
{
    public function index(Request $request)
    {
        // Admin viewing all polls with full vote tracking
        $polls = Poll::with(['options', 'votes.user', 'votes.option'])->latest()->get();
        return response()->json($polls);
    }

    public function active()
    {
        // Fetch all open polls along with vote counts
        $polls = Poll::where('is_active', true)->with(['options' => function($query) {
            $query->withCount('votes');
        }])->latest()->get();

        return response()->json($polls);
    }

    public function store(StorePollRequest $request, CreatePollAction $action)
    {
        $poll = $action->execute($request->validated(), $request->user()->id);
        
        return response()->json($poll, 201);
    }

    public function show(Request $request, Poll $poll)
    {
        // Public endpoint to view a poll and its options with current votes
        $poll->load(['options' => function($query) {
            $query->withCount('votes');
        }]);

        $deviceUuid = $request->query('device_uuid');
        $hasVoted = false;

        if ($deviceUuid) {
            $hasVoted = \App\Models\Vote::where('poll_id', $poll->id)
                ->where('device_uuid', $deviceUuid)
                ->exists();
        }

        return response()->json([
            ...$poll->toArray(),
            'has_voted' => $hasVoted
        ]);
    }

    public function adminShow(Request $request, Poll $poll)
    {
        if (!$request->user()->is_admin && $request->user()->id !== $poll->user_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $poll->load(['options' => function($query) {
            $query->withCount('votes');
        }, 'votes.user', 'votes.option']);
        return response()->json($poll);
    }

    public function update(Request $request, Poll $poll)
    {
        if (!$request->user()->is_admin && $request->user()->id !== $poll->user_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'question' => 'required|string|max:255',
            'options' => 'sometimes|array|min:2',
            'options.*.id' => 'nullable|exists:options,id',
            'options.*.text' => 'required|string|max:255'
        ]);

        $poll->update(['question' => $validated['question']]);

        if (isset($validated['options'])) {
            $existingOptionIds = $poll->options()->pluck('id')->toArray();
            $keptOptionIds = [];

            foreach ($validated['options'] as $opt) {
                if (isset($opt['id']) && in_array($opt['id'], $existingOptionIds)) {
                    $poll->options()->where('id', $opt['id'])->update(['text' => $opt['text']]);
                    $keptOptionIds[] = $opt['id'];
                } else {
                    $newOpt = $poll->options()->create(['text' => $opt['text']]);
                    $keptOptionIds[] = $newOpt->id;
                }
            }

            // Delete removed options
            $optionsToDelete = array_diff($existingOptionIds, $keptOptionIds);
            if (!empty($optionsToDelete)) {
                $poll->options()->whereIn('id', $optionsToDelete)->delete();
            }
        }
        
        // Return fresh poll with relationships for the dashboard
        return response()->json($poll->load(['options', 'votes.user', 'votes.option']));
    }

    public function destroy(Request $request, Poll $poll)
    {
        if (!$request->user()->is_admin && $request->user()->id !== $poll->user_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $poll->delete();

        return response()->json(['message' => 'Poll deleted successfully']);
    }

    public function toggleStatus(Request $request, Poll $poll)
    {
        if (!$request->user()->is_admin && $request->user()->id !== $poll->user_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $poll->update([
            'is_active' => !$poll->is_active
        ]);

        return response()->json($poll->load(['options', 'votes.user', 'votes.option']));
    }
}
