<?php

namespace Database\Seeders;

use App\Models\Classroom;
use App\Models\Course;
use App\Models\Quiz;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class QuizSeeder extends Seeder
{
    public function run(): void
    {
        $teacher = User::where('role', 'teacher')->first();
        if (!$teacher) {
            return;
        }

        $classroom = Classroom::firstOrCreate(
            ['teacher_id' => $teacher->id],
            ['name' => 'Lop Thu Nghiem', 'code' => strtoupper(Str::random(6))]
        );

        $course = Course::firstOrCreate(
            ['classroom_id' => $classroom->id, 'name' => 'Dia Ly Co Ban'],
            ['description' => 'Khoa hoc demo ve thu do the gioi.']
        );

        $exists = Quiz::where('course_id', $course->id)
            ->where('title', 'Thu do cac quoc gia - Test')
            ->exists();
        if ($exists) {
            return;
        }

        $questions = [
            [
                'question_text' => 'Thu do cua Nhat Ban la gi?',
                'options' => ['Tokyo', 'Osaka', 'Kyoto', 'Nagoya'],
                'type' => 'single',
                'correct_options' => [0],
            ],
            [
                'question_text' => 'Thu do cua Phap la gi?',
                'options' => ['Marseille', 'Paris', 'Lyon', 'Nice'],
                'type' => 'single',
                'correct_options' => [1],
            ],
            [
                'question_text' => 'Thu do cua My la gi?',
                'options' => ['New York', 'Washington, D.C.', 'Los Angeles', 'Chicago'],
                'type' => 'single',
                'correct_options' => [1],
            ],
            [
                'question_text' => 'Thu do cua Canada la gi?',
                'options' => ['Toronto', 'Ottawa', 'Vancouver', 'Montreal'],
                'type' => 'single',
                'correct_options' => [1],
            ],
            [
                'question_text' => 'Thu do cua Australia la gi?',
                'options' => ['Sydney', 'Canberra', 'Melbourne', 'Perth'],
                'type' => 'single',
                'correct_options' => [1],
            ],
            [
                'question_text' => 'Thu do cua Brazil la gi?',
                'options' => ['Rio de Janeiro', 'Brasilia', 'Sao Paulo', 'Salvador'],
                'type' => 'single',
                'correct_options' => [1],
            ],
            [
                'question_text' => 'Thu do cua Nga la gi?',
                'options' => ['Saint Petersburg', 'Moscow', 'Kazan', 'Novosibirsk'],
                'type' => 'single',
                'correct_options' => [1],
            ],
            [
                'question_text' => 'Thu do cua Trung Quoc la gi?',
                'options' => ['Shanghai', 'Beijing', 'Guangzhou', 'Shenzhen'],
                'type' => 'single',
                'correct_options' => [1],
            ],
            [
                'question_text' => 'Thu do cua An Do la gi?',
                'options' => ['Mumbai', 'New Delhi', 'Bangalore', 'Kolkata'],
                'type' => 'single',
                'correct_options' => [1],
            ],
            [
                'question_text' => 'Thu do cua Duc la gi?',
                'options' => ['Hamburg', 'Berlin', 'Munich', 'Frankfurt'],
                'type' => 'single',
                'correct_options' => [1],
            ],
            [
                'question_text' => 'Thu do cua Y la gi?',
                'options' => ['Rome', 'Milan', 'Naples', 'Turin'],
                'type' => 'single',
                'correct_options' => [0],
            ],
            [
                'question_text' => 'Thu do cua Tay Ban Nha la gi?',
                'options' => ['Barcelona', 'Madrid', 'Seville', 'Valencia'],
                'type' => 'single',
                'correct_options' => [1],
            ],
            [
                'question_text' => 'Thu do cua Ai Cap la gi?',
                'options' => ['Alexandria', 'Cairo', 'Giza', 'Luxor'],
                'type' => 'single',
                'correct_options' => [1],
            ],
            [
                'question_text' => 'Thu do cua Argentina la gi?',
                'options' => ['Cordoba', 'Buenos Aires', 'Rosario', 'Mendoza'],
                'type' => 'single',
                'correct_options' => [1],
            ],
            [
                'question_text' => 'Thu do cua Mexico la gi?',
                'options' => ['Guadalajara', 'Mexico City', 'Monterrey', 'Puebla'],
                'type' => 'single',
                'correct_options' => [1],
            ],
            [
                'question_text' => 'Nam Phi co nhieu thu do, dau la cac thu do dung?',
                'options' => ['Pretoria', 'Cape Town', 'Bloemfontein', 'Johannesburg'],
                'type' => 'multi',
                'correct_options' => [0, 1, 2],
            ],
            [
                'question_text' => 'Bolivia co nhieu thu do, dau la cac thu do dung?',
                'options' => ['Sucre', 'La Paz', 'Santa Cruz', 'Cochabamba'],
                'type' => 'multi',
                'correct_options' => [0, 1],
            ],
            [
                'question_text' => 'Ha Lan co nhieu thu do, dau la cac thu do dung?',
                'options' => ['Amsterdam', 'The Hague', 'Rotterdam', 'Utrecht'],
                'type' => 'multi',
                'correct_options' => [0, 1],
            ],
            [
                'question_text' => 'Malaysia co nhieu thu do, dau la cac thu do dung?',
                'options' => ['Kuala Lumpur', 'Putrajaya', 'Johor Bahru', 'Penang'],
                'type' => 'multi',
                'correct_options' => [0, 1],
            ],
            [
                'question_text' => 'Sri Lanka co nhieu thu do, dau la cac thu do dung?',
                'options' => ['Sri Jayawardenepura Kotte', 'Colombo', 'Kandy', 'Galle'],
                'type' => 'multi',
                'correct_options' => [0, 1],
            ],
        ];

        Quiz::create([
            'course_id' => $course->id,
            'title' => 'Thu do cac quoc gia - Test',
            'duration' => 30,
            'due_date' => now()->addDays(7),
            'questions' => $questions,
        ]);
    }
}
