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
        $optionId = $data['option_id'];
        $ipAddress = request()->ip();

        // Validate option belongs to poll
        $option = Option::where('id', $optionId)->where('poll_id', $poll->id)->firstOrFail();

        // Check for existing vote by IP or User (if logged in)
        $query = Vote::where('poll_id', $poll->id)
            ->where(function ($q) use ($ipAddress) {
                $q->where('ip_address', $ipAddress);
                
                if (auth('sanctum')->check()) {
                    $q->orWhere('user_id', auth('sanctum')->id());
                }
            });

        if ($query->exists()) {
            throw ValidationException::withMessages([
                'vote' => ['You have already voted on this poll.']
            ]);
        }

        $vote = Vote::create([
            'poll_id' => $poll->id,
            'option_id' => $option->id,
            'ip_address' => $ipAddress,
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
