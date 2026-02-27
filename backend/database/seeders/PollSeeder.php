<?php

namespace Database\Seeders;

use App\Models\Poll;
use App\Models\Option;
use App\Models\User;
use Illuminate\Database\Seeder;

class PollSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::first(); // Grab first user to act as creator

        $polls = [
            [
                'question' => 'What is the best programming language for web development?',
                'options' => ['PHP', 'JavaScript', 'Python', 'TypeScript', 'Ruby'],
            ],
            [
                'question' => 'Which CSS framework do you prefer working with?',
                'options' => ['Tailwind CSS', 'Bootstrap', 'Bulma', 'Foundation'],
            ],
            [
                'question' => 'What is your favorite JavaScript framework or library?',
                'options' => ['Vue.js', 'React', 'Angular', 'Svelte'],
            ],
            [
                'question' => 'Which relational/NoSQL database fits most of your projects?',
                'options' => ['MySQL', 'PostgreSQL', 'MongoDB', 'Redis'],
            ],
            [
                'question' => 'What is your primary operating system for development?',
                'options' => ['macOS', 'Linux (Ubuntu, Arch, etc.)', 'Windows', 'ChromeOS'],
            ],
        ];

        foreach ($polls as $pollData) {
            $poll = Poll::create([
                'user_id' => $admin ? $admin->id : null,
                'question' => $pollData['question'],
                'is_active' => true,
            ]);

            foreach ($pollData['options'] as $optionText) {
                Option::create([
                    'poll_id' => $poll->id,
                    'text' => $optionText,
                ]);
            }
        }
    }
}
