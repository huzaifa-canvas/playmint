<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class OriginalQuizSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        // 1. Create Grades
        $gradesData = [
            ['name' => 'Pre-K', 'order' => 1],
            ['name' => 'Kindergarten', 'order' => 2],
            ['name' => 'Grade 1', 'order' => 3],
            ['name' => 'Grade 2', 'order' => 4],
            ['name' => 'Grade 3', 'order' => 5],
            ['name' => 'Grade 4', 'order' => 6],
            ['name' => 'Grade 5', 'order' => 7],
        ];

        foreach ($gradesData as $g) {
            DB::table('grades')->updateOrInsert(['name' => $g['name']], [
                'order' => $g['order'],
                'created_at' => $now,
                'updated_at' => $now
            ]);
        }

        // 2. Create Subjects
        $subjectsData = ['Science', 'Math', 'English', 'Gen. Knowledge', 'Arts', 'Biology'];
        foreach ($subjectsData as $s) {
            DB::table('subjects')->updateOrInsert(['name' => $s], [
                'created_at' => $now,
                'updated_at' => $now
            ]);
        }

        $dbGrades = DB::table('grades')->pluck('id')->toArray();
        $dbSubjects = DB::table('subjects')->pluck('id', 'name')->toArray();

        // Ensure database is clean of questions to avoid duplicates
        DB::table('grade_question')->delete();
        DB::table('questions')->delete();

        // 3. Define Real Questions
        $questions = [
            // Science
            [
                'subject' => 'Science',
                'text' => 'What planet is known as the Red Planet?',
                'options' => ['Earth', 'Mars', 'Jupiter', 'Venus'],
                'correct' => 'b' // Mars
            ],
            [
                'subject' => 'Science',
                'text' => 'What do plants need to make their own food?',
                'options' => ['Moonlight', 'Sugar', 'Sunlight', 'Fire'],
                'correct' => 'c'
            ],
            [
                'subject' => 'Science',
                'text' => 'How many legs does a spider have?',
                'options' => ['6', '8', '10', '4'],
                'correct' => 'b'
            ],
            [
                'subject' => 'Science',
                'text' => 'What is water made of?',
                'options' => ['Hydrogen and Oxygen', 'Helium and Nitrogen', 'Carbon and Oxygen', 'Iron and Water'],
                'correct' => 'a'
            ],
            [
                'subject' => 'Science',
                'text' => 'What falls from clouds when it rains?',
                'options' => ['Snow', 'Water', 'Leaves', 'Rocks'],
                'correct' => 'b'
            ],

            // Math
            [
                'subject' => 'Math',
                'text' => 'What is 5 + 7?',
                'options' => ['10', '11', '12', '13'],
                'correct' => 'c'
            ],
            [
                'subject' => 'Math',
                'text' => 'How many sides does a triangle have?',
                'options' => ['2', '3', '4', '5'],
                'correct' => 'b'
            ],
            [
                'subject' => 'Math',
                'text' => 'If you have 10 apples and eat 3, how many are left?',
                'options' => ['5', '6', '7', '8'],
                'correct' => 'c'
            ],
            [
                'subject' => 'Math',
                'text' => 'What is 4 multiplied by 3?',
                'options' => ['7', '10', '12', '16'],
                'correct' => 'c'
            ],
            [
                'subject' => 'Math',
                'text' => 'Which number is the largest?',
                'options' => ['45', '72', '39', '68'],
                'correct' => 'b'
            ],

            // English
            [
                'subject' => 'English',
                'text' => 'Which of these is a vowel?',
                'options' => ['B', 'Z', 'E', 'M'],
                'correct' => 'c'
            ],
            [
                'subject' => 'English',
                'text' => 'What is the opposite of "Hot"?',
                'options' => ['Warm', 'Cold', 'Spicy', 'Boiling'],
                'correct' => 'b'
            ],
            [
                'subject' => 'English',
                'text' => 'Choose the correct spelling:',
                'options' => ['Bicycle', 'Bicycal', 'Bisycle', 'Bycicle'],
                'correct' => 'a'
            ],
            [
                'subject' => 'English',
                'text' => 'Which word is a noun?',
                'options' => ['Run', 'Quickly', 'Happy', 'Dog'],
                'correct' => 'd'
            ],
            [
                'subject' => 'English',
                'text' => 'What do we put at the end of a question?',
                'options' => ['Comma', 'Period', 'Question Mark', 'Exclamation Mark'],
                'correct' => 'c'
            ],

            // Gen. Knowledge
            [
                'subject' => 'Gen. Knowledge',
                'text' => 'Which animal is the king of the jungle?',
                'options' => ['Tiger', 'Lion', 'Elephant', 'Bear'],
                'correct' => 'b'
            ],
            [
                'subject' => 'Gen. Knowledge',
                'text' => 'How many days are there in a week?',
                'options' => ['5', '6', '7', '8'],
                'correct' => 'c'
            ],
            [
                'subject' => 'Gen. Knowledge',
                'text' => 'What color is the sky on a clear day?',
                'options' => ['Green', 'Blue', 'Red', 'Yellow'],
                'correct' => 'b'
            ],
            [
                'subject' => 'Gen. Knowledge',
                'text' => 'Which country is famous for the Eiffel Tower?',
                'options' => ['Italy', 'Spain', 'France', 'Germany'],
                'correct' => 'c'
            ],
            [
                'subject' => 'Gen. Knowledge',
                'text' => 'What do cows drink?',
                'options' => ['Milk', 'Juice', 'Water', 'Coffee'],
                'correct' => 'c'
            ],

            // Arts
            [
                'subject' => 'Arts',
                'text' => 'What do you get when you mix Red and Yellow?',
                'options' => ['Green', 'Purple', 'Orange', 'Brown'],
                'correct' => 'c'
            ],
            [
                'subject' => 'Arts',
                'text' => 'What tool do you use to paint on a canvas?',
                'options' => ['Hammer', 'Paintbrush', 'Pencil', 'Spoon'],
                'correct' => 'b'
            ],
            [
                'subject' => 'Arts',
                'text' => 'Which of these is a primary color?',
                'options' => ['Green', 'Purple', 'Blue', 'Orange'],
                'correct' => 'c'
            ],
            [
                'subject' => 'Arts',
                'text' => 'What material is origami made from?',
                'options' => ['Wood', 'Metal', 'Paper', 'Plastic'],
                'correct' => 'c'
            ],
            [
                'subject' => 'Arts',
                'text' => 'Who painted the Mona Lisa?',
                'options' => ['Vincent van Gogh', 'Leonardo da Vinci', 'Pablo Picasso', 'Claude Monet'],
                'correct' => 'b'
            ],

            // Biology
            [
                'subject' => 'Biology',
                'text' => 'What part of the plant absorbs water?',
                'options' => ['Leaves', 'Stem', 'Roots', 'Flowers'],
                'correct' => 'c'
            ],
            [
                'subject' => 'Biology',
                'text' => 'Which animal breathes underwater using gills?',
                'options' => ['Dog', 'Fish', 'Bird', 'Cat'],
                'correct' => 'b'
            ],
            [
                'subject' => 'Biology',
                'text' => 'What is the human body\'s largest organ?',
                'options' => ['Heart', 'Brain', 'Liver', 'Skin'],
                'correct' => 'd'
            ],
            [
                'subject' => 'Biology',
                'text' => 'Which of these is a mammal?',
                'options' => ['Snake', 'Frog', 'Dolphin', 'Shark'],
                'correct' => 'c'
            ],
            [
                'subject' => 'Biology',
                'text' => 'What do bees collect from flowers?',
                'options' => ['Leaves', 'Nectar', 'Water', 'Seeds'],
                'correct' => 'b'
            ]
        ];

        $questionsToInsert = [];
        $gradeQuestionToInsert = [];

        foreach ($questions as $q) {
            $questionId = DB::table('questions')->insertGetId([
                'subject_id' => $dbSubjects[$q['subject']],
                'question_text' => $q['text'],
                'option_a' => $q['options'][0],
                'option_b' => $q['options'][1],
                'option_c' => $q['options'][2],
                'option_d' => $q['options'][3],
                'correct_option' => $q['correct'],
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            // Attach this question to ALL grades (Pre-K through Grade 5)
            foreach ($dbGrades as $gradeId) {
                $gradeQuestionToInsert[] = [
                    'grade_id' => $gradeId,
                    'question_id' => $questionId
                ];
            }
        }
        
        DB::table('grade_question')->insert($gradeQuestionToInsert);
    }
}
