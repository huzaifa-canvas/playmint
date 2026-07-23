<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class QuizSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        // 1. Create Grades
        $grades = [
            ['name' => 'Pre-K', 'order' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Kindergarten', 'order' => 2, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Grade 1', 'order' => 3, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Grade 2', 'order' => 4, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Grade 3', 'order' => 5, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Grade 4', 'order' => 6, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Grade 5', 'order' => 7, 'created_at' => $now, 'updated_at' => $now],
        ];

        DB::table('grades')->insert($grades);

        // 2. Create Subjects
        $subjects = [
            ['name' => 'Science', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Math', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'English', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Gen. Knowledge', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Arts', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Biology', 'created_at' => $now, 'updated_at' => $now]
        ];

        DB::table('subjects')->insert($subjects);

        $dbGrades = DB::table('grades')->get();
        $dbSubjects = DB::table('subjects')->get();

        $optionsList = [
            ['a', 'b', 'c', 'd'],
            ['b', 'c', 'd', 'a'],
            ['c', 'd', 'a', 'b'],
            ['d', 'a', 'b', 'c']
        ];

        $questionsToInsert = [];
        $gradeQuestionToInsert = [];
        $questionId = 1;

        // 3. Create 5 Unique Questions for EVERY Subject and Grade combination
        foreach ($dbSubjects as $subject) {
            foreach ($dbGrades as $grade) {
                for ($i = 1; $i <= 5; $i++) {
                    
                    $questionText = "{$subject->name} Question {$i} specifically designed for {$grade->name} students. Unique ID: " . Str::random(5);

                    // Pick random options pattern
                    $optPattern = $optionsList[array_rand($optionsList)];
                    $correct = array_rand(array_flip(['a', 'b', 'c', 'd']));

                    $questionsToInsert[] = [
                        'subject_id' => $subject->id,
                        'question_text' => $questionText,
                        'option_a' => 'Option ' . strtoupper($optPattern[0]),
                        'option_b' => 'Option ' . strtoupper($optPattern[1]),
                        'option_c' => 'Option ' . strtoupper($optPattern[2]),
                        'option_d' => 'Option ' . strtoupper($optPattern[3]),
                        'correct_option' => $correct,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];

                    $gradeQuestionToInsert[] = [
                        'grade_id' => $grade->id,
                        'question_id' => $questionId
                    ];
                    
                    $questionId++;
                }
            }
        }

        // Chunk inserts to prevent SQLite parameter limits (max 999 vars)
        foreach (array_chunk($questionsToInsert, 100) as $chunk) {
            DB::table('questions')->insert($chunk);
        }

        foreach (array_chunk($gradeQuestionToInsert, 100) as $chunk) {
            DB::table('grade_question')->insert($chunk);
        }
    }
}
