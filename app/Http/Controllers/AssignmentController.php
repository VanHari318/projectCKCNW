<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Course;
use App\Models\Grade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AssignmentController extends Controller
{
    public function store(Request $request, Course $course)
    {
        $user = Auth::user();
        if (!$user->isTeacher() || $course->classroom->teacher_id !== $user->id) {
            abort(403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'duration' => 'required|integer|min:1',
            'due_date' => 'required|date',
            'questions' => 'required|array|min:1',
            'questions.*.question_text' => 'required|string',
            'questions.*.options' => 'required|array|size:4',
            'questions.*.options.*' => 'required|string',
            'questions.*.correct_index' => 'required|integer|min:0|max:3',
        ]);

        Assignment::create([
            'course_id' => $course->id,
            'title' => $request->title,
            'duration' => $request->duration,
            'due_date' => $request->due_date,
            'questions' => $request->questions,
        ]);

        return back()->with('success', 'Đã giao bài tập trắc nghiệm mới thành công!');
    }

    public function take(Assignment $assignment)
    {
        $user = Auth::user();
        if (!$user->isStudent()) {
            abort(403);
        }

        $course = $assignment->course;
        // Verify student is enrolled in course
        if (!$course->students()->where('student_id', $user->id)->exists()) {
            abort(403, 'Bạn chưa tham gia khóa học này.');
        }

        // Check if already taken
        $existing = Grade::where('student_id', $user->id)
            ->where('gradeable_type', 'App\\Models\\Assignment')
            ->where('gradeable_id', $assignment->id)
            ->first();

        if ($existing) {
            return redirect()->route('course.show', $course->id)
                ->with('error', 'Bạn đã hoàn thành bài tập này từ trước.');
        }

        // Check if expired
        if (now()->greaterThan($assignment->due_date)) {
            return redirect()->route('course.show', $course->id)
                ->with('error', 'Hạn làm bài tập này đã kết thúc.');
        }

        return view('assignment.take', [
            'assignment' => $assignment,
            'course' => $course,
        ]);
    }

    public function submit(Request $request, Assignment $assignment)
    {
        $user = Auth::user();
        if (!$user->isStudent()) {
            abort(403);
        }

        $course = $assignment->course;
        // Verify student is enrolled in course
        if (!$course->students()->where('student_id', $user->id)->exists()) {
            abort(403, 'Bạn chưa tham gia khóa học này.');
        }

        // Check if already taken
        $existing = Grade::where('student_id', $user->id)
            ->where('gradeable_type', 'App\\Models\\Assignment')
            ->where('gradeable_id', $assignment->id)
            ->first();

        if ($existing) {
            return redirect()->route('course.show', $course->id)
                ->with('error', 'Bạn đã làm bài tập này rồi.');
        }

        // Parse questions
        $questions = $assignment->questions;
        $totalQuestions = count($questions);
        
        $answers = $request->input('answers', []); // [question_index => selected_index]
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
            'gradeable_id' => $assignment->id,
            'gradeable_type' => 'App\\Models\\Assignment',
            'score' => $score,
            'submission_content' => json_encode($submissionDetails, JSON_UNESCAPED_UNICODE),
            'submitted_at' => now(),
        ]);

        return redirect()->route('course.show', $course->id)
            ->with('success', "Nộp bài tập thành công! Điểm của bạn: {$score}/10");
    }

    // Teacher grades/overrides an assignment score
    public function grade(Request $request, Grade $grade)
    {
        $user = Auth::user();
        $course = $grade->course;
        if (!$user->isTeacher() || $course->classroom->teacher_id !== $user->id) {
            abort(403);
        }

        $request->validate([
            'score' => 'required|numeric|min:0|max:10',
        ]);

        $grade->update([
            'score' => $request->score,
        ]);

        return back()->with('success', 'Đã cập nhật điểm bài tập thành công!');
    }

    // Teacher deletes an assignment
    public function destroy(Assignment $assignment)
    {
        $user = Auth::user();
        $course = $assignment->course;
        if (!$user->isTeacher() || $course->classroom->teacher_id !== $user->id) {
            abort(403);
        }

        $assignment->delete();

        return back()->with('success', 'Đã xóa bài tập trắc nghiệm thành công.');
    }
}
