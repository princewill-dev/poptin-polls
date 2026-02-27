<?php

namespace App\Http\Controllers;

use App\Actions\RegisterVoteAction;
use App\Models\Poll;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class VoteController extends Controller
{
    public function store(Request $request, string $pollId, RegisterVoteAction $action)
    {
        $data = $request->validate([
            'option_id' => ['required', 'integer', 'exists:options,id'],
            'device_uuid' => ['required', 'string', 'max:255'],
        ]);

        $poll = Poll::findOrFail($pollId);

        try {
            $action->execute($poll, $data);
            return response()->json(['message' => 'Vote recorded successfully.']);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'errors' => $e->errors()
            ], 422);
        }
    }
}
