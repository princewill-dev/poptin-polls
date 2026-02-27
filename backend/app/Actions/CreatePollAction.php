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
                'slug' => str_pad(random_int(0, 99999999), 8, '0', STR_PAD_LEFT),
            ]);

            $options = collect($data['options'])->map(fn ($text) => ['text' => $text]);
            
            $poll->options()->createMany($options->toArray());

            return $poll->load('options');
        });
    }
}
