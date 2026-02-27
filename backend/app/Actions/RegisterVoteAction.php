<?php

namespace App\Actions;

use App\Events\VoteRegistered;
use App\Models\Option;
use App\Models\Poll;
use App\Models\Vote;
use Illuminate\Validation\ValidationException;

class RegisterVoteAction
{
    public function execute(Poll $poll, array $data): Vote
    {
        $deviceUuid = $data['device_uuid'];
        $optionId = $data['option_id'];

        // Validate option belongs to poll
        $option = Option::where('id', $optionId)->where('poll_id', $poll->id)->firstOrFail();

        // Check for existing vote
        if (Vote::where('poll_id', $poll->id)->where('device_uuid', $deviceUuid)->exists()) {
            throw ValidationException::withMessages([
                'device_uuid' => ['You have already voted on this poll.']
            ]);
        }

        $vote = Vote::create([
            'poll_id' => $poll->id,
            'option_id' => $option->id,
            'device_uuid' => $deviceUuid,
            'user_id' => auth('sanctum')->id(),
        ]);

        // Fetch updated poll with counts to broadcast
        $updatedPoll = Poll::with(['options' => function($query) {
            $query->withCount('votes');
        }])->find($poll->id);

        broadcast(new VoteRegistered($updatedPoll));

        return $vote;
    }
}
