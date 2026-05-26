<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Grade;
use App\Models\Quiz;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuizController extends Controller
{
    public function store(Request $request, Course $course)
    {
        /** @var User $user */
        $user = Auth::user();
        if (!$user->isTeacher() || $course->classroom->teacher_id !== $user->id) {
            abort(403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'duration' => 'required|integer|min:1',
            'due_date' => 'required|date',
            // questions will be passed as a structured array
            'questions' => 'required|array|min:1',
            'questions.*.question_text' => 'required|string',
            'questions.*.options' => 'required|array|size:4',
            'questions.*.options.*' => 'required|string',
            'questions.*.correct_index' => 'required|integer|min:0|max:3',
        ]);

        Quiz::create([
            'course_id' => $course->id,
            'title' => $request->title,
            'duration' => $request->duration,
            'due_date' => $request->due_date,
            'questions' => $request->questions,
        ]);

        return back()->with('success', 'Đã tạo đề kiểm tra trắc nghiệm thành công!');
    }

    public function take(Quiz $quiz)
    {
        /** @var User $user */
        $user = Auth::user();
        if (!$user->isStudent()) {
            abort(403);
        }

        $course = $quiz->course;
        // Verify student is enrolled in course
        if (!$course->students()->where('student_id', $user->id)->exists()) {
            abort(403, 'Bạn chưa tham gia khóa học này.');
        }

        // Check if already taken
        $existing = Grade::where('student_id', $user->id)
            ->where('gradeable_type', 'App\\Models\\Quiz')
            ->where('gradeable_id', $quiz->id)
            ->first();

        if ($existing) {
            return redirect()->route('course.show', $course->id)
                ->with('error', 'Bạn đã hoàn thành bài kiểm tra này từ trước.');
        }

        // Check if expired
        if (now()->greaterThan($quiz->due_date)) {
            return redirect()->route('course.show', $course->id)
                ->with('error', 'Hạn làm bài kiểm tra này đã kết thúc.');
        }

        return view('quiz.take', [
            'quiz' => $quiz,
            'course' => $course,
        ]);
    }

    public function submit(Request $request, Quiz $quiz)
    {
        /** @var User $user */
        $user = Auth::user();
        if (!$user->isStudent()) {
            abort(403);
        }

        $course = $quiz->course;
        // Verify student is enrolled in course
        if (!$course->students()->where('student_id', $user->id)->exists()) {
            abort(403, 'Bạn chưa tham gia khóa học này.');
        }

        // Check if already taken
        $existing = Grade::where('student_id', $user->id)
            ->where('gradeable_type', 'App\\Models\\Quiz')
            ->where('gradeable_id', $quiz->id)
            ->first();

        if ($existing) {
            return redirect()->route('course.show', $course->id)
                ->with('error', 'Bạn đã làm bài kiểm tra này rồi.');
        }

        // Parse questions
        $questions = $quiz->questions;
        $totalQuestions = count($questions);
        
        $answers = $request->input('answers', []); // array of selected indices: [question_index => selected_index]
        $correctCount = 0;

        $submissionDetails = [];

        foreach ($questions as $index => $q) {
            $selectedOptionIndex = isset($answers[$index]) ? intval($answers[$index]) : null;
            $correctOptionIndex = intval($q['correct_index']);

            $isCorrect = ($selectedOptionIndex !== null && $selectedOptionIndex === $correctOptionIndex);
            if ($isCorrect) {
                $correctCount++;
            }

            $submissionDetails[] = [
                'question_text' => $q['question_text'],
                'options' => $q['options'],
                'selected_option' => $selectedOptionIndex !== null ? $q['options'][$selectedOptionIndex] : 'Không chọn',
                'correct_option' => $q['options'][$correctOptionIndex],
                'is_correct' => $isCorrect
            ];
        }

        // Calculate score out of 10
        $score = $totalQuestions > 0 ? round(($correctCount / $totalQuestions) * 10, 2) : 0;

        // Save Grade (polymorphic)
        Grade::create([
            'classroom_id' => $course->classroom_id,
            'course_id' => $course->id,
            'student_id' => $user->id,
            'gradeable_id' => $quiz->id,
            'gradeable_type' => 'App\\Models\\Quiz',
            'score' => $score,
            'submission_content' => json_encode($submissionDetails, JSON_UNESCAPED_UNICODE),
            'submitted_at' => now(),
        ]);

        return redirect()->route('course.show', $course->id)
            ->with('success', "Nộp bài kiểm tra thành công! Điểm của bạn: {$score}/10");
    }

    public function destroy(Quiz $quiz)
    {
        /** @var User $user */
        $user = Auth::user();
        $course = $quiz->course;
        if (!$user->isTeacher() || $course->classroom->teacher_id !== $user->id) {
            abort(403);
        }

        $quiz->delete();

        return back()->with('success', 'Đã xóa bài kiểm tra trắc nghiệm thành công.');
    }
}
