<?php

namespace Database\Seeders;

use App\Models\Assignment;
use App\Models\Classroom;
use App\Models\Course;
use App\Models\Quiz;
use App\Models\Room;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class QuizSeeder extends Seeder
{
    public function run(): void
    {
        $teacher = User::where('role', 'teacher')->first();
        $student = User::where('role', 'student')->first();
        if (!$teacher) {
            return;
        }

        $classroom = Classroom::firstOrCreate(
            ['teacher_id' => $teacher->id],
            ['name' => 'Lớp Thử Nghiệm', 'code' => strtoupper(Str::random(6))]
        );

        $course = Course::firstOrCreate(
            ['classroom_id' => $classroom->id, 'name' => 'Địa Lý Cơ Bản'],
            ['description' => 'Khóa học demo về thủ đô và châu lục.']
        );

        if ($student) {
            $classroom->students()->syncWithoutDetaching([
                $student->id => ['status' => 'approved'],
            ]);
            $course->students()->syncWithoutDetaching([$student->id]);
        }

        Room::firstOrCreate(
            ['course_id' => $course->id, 'title' => 'Phòng học thử nghiệm'],
            [
                'join_url' => 'https://meet.google.com/abc-defg-hij',
                'scheduled_at' => now()->addDays(1),
                'is_active' => true,
            ]
        );

        $assignmentTitle = 'Bài tập: Châu lục - Thử nghiệm';
        $assignmentExists = Assignment::where('course_id', $course->id)
            ->where('title', $assignmentTitle)
            ->exists();

        if (!$assignmentExists) {
            $assignmentQuestions = [
                [
                    'question_text' => 'Châu lục nào lớn nhất thế giới?',
                    'options' => ['Châu Phi', 'Châu Âu', 'Châu Á', 'Châu Mỹ'],
                    'type' => 'single',
                    'correct_options' => [2],
                ],
                [
                    'question_text' => 'Việt Nam nằm ở châu lục nào?',
                    'options' => ['Châu Á', 'Châu Âu', 'Châu Đại Dương', 'Châu Mỹ'],
                    'type' => 'single',
                    'correct_options' => [0],
                ],
                [
                    'question_text' => 'Châu lục nào có nhiều quốc gia nhất?',
                    'options' => ['Châu Phi', 'Châu Âu', 'Châu Á', 'Châu Mỹ'],
                    'type' => 'single',
                    'correct_options' => [0],
                ],
                [
                    'question_text' => 'Quốc gia nào thuộc châu Đại Dương?',
                    'options' => ['Australia', 'Canada', 'Mexico', 'Spain'],
                    'type' => 'single',
                    'correct_options' => [0],
                ],
            ];

            Assignment::create([
                'course_id' => $course->id,
                'title' => $assignmentTitle,
                'duration' => 15,
                'due_date' => now()->addDays(5),
                'questions' => $assignmentQuestions,
            ]);
        }

        $quizTitle = 'Bài kiểm tra: Thủ đô các quốc gia';
        $exists = Quiz::where('course_id', $course->id)
            ->where('title', $quizTitle)
            ->exists();
        if ($exists) {
            return;
        }

        $questions = [
            [
                'question_text' => 'Thủ đô của Nhật Bản là gì?',
                'options' => ['Tokyo', 'Osaka', 'Kyoto', 'Nagoya'],
                'type' => 'single',
                'correct_options' => [0],
            ],
            [
                'question_text' => 'Thủ đô của Pháp là gì?',
                'options' => ['Marseille', 'Paris', 'Lyon', 'Nice'],
                'type' => 'single',
                'correct_options' => [1],
            ],
            [
                'question_text' => 'Thủ đô của Mỹ là gì?',
                'options' => ['New York', 'Washington, D.C.', 'Los Angeles', 'Chicago'],
                'type' => 'single',
                'correct_options' => [1],
            ],
            [
                'question_text' => 'Thủ đô của Canada là gì?',
                'options' => ['Toronto', 'Ottawa', 'Vancouver', 'Montreal'],
                'type' => 'single',
                'correct_options' => [1],
            ],
            [
                'question_text' => 'Thủ đô của Australia là gì?',
                'options' => ['Sydney', 'Canberra', 'Melbourne', 'Perth'],
                'type' => 'single',
                'correct_options' => [1],
            ],
            [
                'question_text' => 'Thủ đô của Brazil là gì?',
                'options' => ['Rio de Janeiro', 'Brasilia', 'Sao Paulo', 'Salvador'],
                'type' => 'single',
                'correct_options' => [1],
            ],
            [
                'question_text' => 'Thủ đô của Nga là gì?',
                'options' => ['Saint Petersburg', 'Moscow', 'Kazan', 'Novosibirsk'],
                'type' => 'single',
                'correct_options' => [1],
            ],
            [
                'question_text' => 'Thủ đô của Trung Quốc là gì?',
                'options' => ['Shanghai', 'Beijing', 'Guangzhou', 'Shenzhen'],
                'type' => 'single',
                'correct_options' => [1],
            ],
            [
                'question_text' => 'Thủ đô của Ấn Độ là gì?',
                'options' => ['Mumbai', 'New Delhi', 'Bangalore', 'Kolkata'],
                'type' => 'single',
                'correct_options' => [1],
            ],
            [
                'question_text' => 'Thủ đô của Đức là gì?',
                'options' => ['Hamburg', 'Berlin', 'Munich', 'Frankfurt'],
                'type' => 'single',
                'correct_options' => [1],
            ],
            [
                'question_text' => 'Thủ đô của Ý là gì?',
                'options' => ['Rome', 'Milan', 'Naples', 'Turin'],
                'type' => 'single',
                'correct_options' => [0],
            ],
            [
                'question_text' => 'Thủ đô của Tây Ban Nha là gì?',
                'options' => ['Barcelona', 'Madrid', 'Seville', 'Valencia'],
                'type' => 'single',
                'correct_options' => [1],
            ],
            [
                'question_text' => 'Thủ đô của Ai Cập là gì?',
                'options' => ['Alexandria', 'Cairo', 'Giza', 'Luxor'],
                'type' => 'single',
                'correct_options' => [1],
            ],
            [
                'question_text' => 'Thủ đô của Argentina là gì?',
                'options' => ['Cordoba', 'Buenos Aires', 'Rosario', 'Mendoza'],
                'type' => 'single',
                'correct_options' => [1],
            ],
            [
                'question_text' => 'Thủ đô của Mexico là gì?',
                'options' => ['Guadalajara', 'Mexico City', 'Monterrey', 'Puebla'],
                'type' => 'single',
                'correct_options' => [1],
            ],
            [
                'question_text' => 'Nam Phi có nhiều thủ đô, đâu là các thủ đô đúng?',
                'options' => ['Pretoria', 'Cape Town', 'Bloemfontein', 'Johannesburg'],
                'type' => 'multi',
                'correct_options' => [0, 1, 2],
            ],
            [
                'question_text' => 'Bolivia có nhiều thủ đô, đâu là các thủ đô đúng?',
                'options' => ['Sucre', 'La Paz', 'Santa Cruz', 'Cochabamba'],
                'type' => 'multi',
                'correct_options' => [0, 1],
            ],
            [
                'question_text' => 'Hà Lan có nhiều thủ đô, đâu là các thủ đô đúng?',
                'options' => ['Amsterdam', 'The Hague', 'Rotterdam', 'Utrecht'],
                'type' => 'multi',
                'correct_options' => [0, 1],
            ],
            [
                'question_text' => 'Malaysia có nhiều thủ đô, đâu là các thủ đô đúng?',
                'options' => ['Kuala Lumpur', 'Putrajaya', 'Johor Bahru', 'Penang'],
                'type' => 'multi',
                'correct_options' => [0, 1],
            ],
            [
                'question_text' => 'Sri Lanka có nhiều thủ đô, đâu là các thủ đô đúng?',
                'options' => ['Sri Jayawardenepura Kotte', 'Colombo', 'Kandy', 'Galle'],
                'type' => 'multi',
                'correct_options' => [0, 1],
            ],
        ];

        Quiz::create([
            'course_id' => $course->id,
            'title' => $quizTitle,
            'duration' => 30,
            'due_date' => now()->addDays(7),
            'questions' => $questions,
        ]);
    }
}
