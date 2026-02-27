<?php

namespace App\Actions;

use App\Models\Poll;
use Illuminate\Support\Facades\DB;

class CreatePollAction
{
    public function execute(array $data, int $userId): Poll
    {
        return DB::transaction(function () use ($data, $userId) {
            $poll = Poll::create([
                'question' => $data['question'],
                'user_id' => $userId,
            ]);

            $options = collect($data['options'])->map(fn ($text) => ['text' => $text]);
            
            $poll->options()->createMany($options->toArray());

            return $poll->load('options');
        });
    }
}
