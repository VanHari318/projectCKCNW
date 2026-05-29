<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Course;
use App\Models\Grade;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AssignmentController extends Controller
{
    public function storeForTeacher(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();
        if (!$user->isTeacher()) {
            abort(403);
        }

        $request->validate([
            'scope' => 'required|in:course,classroom',
            'classroom_id' => 'required|integer|exists:classrooms,id',
            'course_id' => 'required_if:scope,course|nullable|integer|exists:courses,id',
            'title' => 'required|string|max:255',
            'duration' => 'required|integer|min:1',
            'due_date' => 'required|date',
            'questions' => 'required|array|min:1',
            'questions.*.question_text' => 'required|string',
            'questions.*.options' => 'required|array|size:4',
            'questions.*.options.*' => 'required|string',
            'questions.*.type' => 'required|in:single,multi',
            'questions.*.correct_options' => 'required|array|min:1',
            'questions.*.correct_options.*' => 'required|integer|min:0|max:3',
        ]);

        $classroom = $user->classroom;
        if (!$classroom || $classroom->id !== intval($request->classroom_id)) {
            abort(403);
        }

        $questions = $this->normalizeQuestions($request->questions);

        if ($request->scope === 'classroom') {
            $courses = $classroom->courses;
            if ($courses->isEmpty()) {
                return back()->with('error', 'Lớp học chưa có khóa học nào.');
            }

            foreach ($courses as $course) {
                Assignment::create([
                    'course_id' => $course->id,
                    'title' => $request->title,
                    'duration' => $request->duration,
                    'due_date' => $request->due_date,
                    'questions' => $questions,
                ]);
            }

            return back()->with('success', 'Đã giao bài tập cho toàn bộ khóa học trong lớp.');
        }

        $course = Course::where('id', $request->course_id)
            ->where('classroom_id', $classroom->id)
            ->first();
        if (!$course) {
            abort(403);
        }

        Assignment::create([
            'course_id' => $course->id,
            'title' => $request->title,
            'duration' => $request->duration,
            'due_date' => $request->due_date,
            'questions' => $questions,
        ]);

        return back()->with('success', 'Đã giao bài tập cho khóa học đã chọn.');
    }

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
            'questions' => 'required|array|min:1',
            'questions.*.question_text' => 'required|string',
            'questions.*.options' => 'required|array|size:4',
            'questions.*.options.*' => 'required|string',
            'questions.*.type' => 'required|in:single,multi',
            'questions.*.correct_options' => 'required|array|min:1',
            'questions.*.correct_options.*' => 'required|integer|min:0|max:3',
        ]);

        Assignment::create([
            'course_id' => $course->id,
            'title' => $request->title,
            'duration' => $request->duration,
            'due_date' => $request->due_date,
            'questions' => $this->normalizeQuestions($request->questions),
        ]);

        return back()->with('success', 'Đã giao bài tập trắc nghiệm mới thành công!');
    }

    public function edit(Assignment $assignment)
    {
        /** @var User $user */
        $user = Auth::user();
        $course = $assignment->course;
        if (!$user->isTeacher() || $course->classroom->teacher_id !== $user->id) {
            abort(403);
        }

        $shareCourses = $course->classroom->courses()
            ->where('id', '!=', $course->id)
            ->get();

        return view('teacher.assignments.edit', [
            'assignment' => $assignment,
            'course' => $course,
            'shareCourses' => $shareCourses,
        ]);
    }

    public function update(Request $request, Assignment $assignment)
    {
        /** @var User $user */
        $user = Auth::user();
        $course = $assignment->course;
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
            'questions.*.type' => 'required|in:single,multi',
            'questions.*.correct_options' => 'required|array|min:1',
            'questions.*.correct_options.*' => 'required|integer|min:0|max:3',
            'share_course_ids' => 'sometimes|array',
            'share_course_ids.*' => 'integer|exists:courses,id',
        ]);

        $questions = $this->normalizeQuestions($request->questions);

        $assignment->update([
            'title' => $request->title,
            'duration' => $request->duration,
            'due_date' => $request->due_date,
            'questions' => $questions,
        ]);

        $shareCourseIds = collect($request->input('share_course_ids', []))
            ->map(fn($id) => (int) $id)
            ->filter()
            ->unique();
        $sharedCount = 0;

        if ($shareCourseIds->isNotEmpty()) {
            $shareCourses = $course->classroom->courses()
                ->whereIn('id', $shareCourseIds)
                ->where('id', '!=', $course->id)
                ->get();

            foreach ($shareCourses as $shareCourse) {
                Assignment::create([
                    'course_id' => $shareCourse->id,
                    'title' => $request->title,
                    'duration' => $request->duration,
                    'due_date' => $request->due_date,
                    'questions' => $questions,
                ]);
            }

            $sharedCount = $shareCourses->count();
        }

        $message = 'Đã cập nhật bài tập.';
        if ($sharedCount > 0) {
            $message .= ' Đã chia sẻ sang ' . $sharedCount . ' khóa học.';
        }

        return redirect()->route('teacher.assignments.edit', $assignment->id)
            ->with('success', $message);
    }

    public function take(Assignment $assignment)
    {
        /** @var User $user */
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
            return redirect()->route('student.courses.show', $course->id)
                ->with('error', 'Bạn đã hoàn thành bài tập này từ trước.');
        }

        // Check if expired
        if (now()->greaterThan($assignment->due_date)) {
            return redirect()->route('student.courses.show', $course->id)
                ->with('error', 'Hạn làm bài tập này đã kết thúc.');
        }

        return view('assignment.take', [
            'assignment' => $assignment,
            'course' => $course,
        ]);
    }

    public function submit(Request $request, Assignment $assignment)
    {
        /** @var User $user */
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
            return redirect()->route('student.courses.show', $course->id)
                ->with('error', 'Bạn đã làm bài tập này rồi.');
        }

        // Parse questions
        $questions = $assignment->questions;
        $totalQuestions = count($questions);
        
        $answers = $request->input('answers', []); // [question_index => selected_index or array]
        $correctScore = 0;
        $submissionDetails = [];

        foreach ($questions as $index => $q) {
            $correctOptions = array_map('intval', $q['correct_options'] ?? []);
            $selectedOptions = $answers[$index] ?? [];
            if (!is_array($selectedOptions)) {
                $selectedOptions = [$selectedOptions];
            }
            $selectedOptions = array_values(array_unique(array_map('intval', $selectedOptions)));

            $matched = array_values(array_intersect($correctOptions, $selectedOptions));
            $questionScore = count($correctOptions) > 0 ? count($matched) / count($correctOptions) : 0;
            $correctScore += $questionScore;

            $submissionDetails[] = [
                'question_text' => $q['question_text'],
                'options' => $q['options'],
                'selected_options' => $selectedOptions,
                'correct_options' => $correctOptions,
                'question_score' => round($questionScore, 4),
            ];
        }

        // Calculate score out of 10
        $score = $totalQuestions > 0 ? round(($correctScore / $totalQuestions) * 10, 2) : 0;

        // Save Grade (polymorphic)
        $grade = Grade::create([
            'classroom_id' => $course->classroom_id,
            'course_id' => $course->id,
            'student_id' => $user->id,
            'gradeable_id' => $assignment->id,
            'gradeable_type' => 'App\\Models\\Assignment',
            'score' => $score,
            'submission_content' => json_encode($submissionDetails, JSON_UNESCAPED_UNICODE),
            'submitted_at' => now(),
        ]);

        return redirect()->route('student.assignments.review', $assignment->id)
            ->with('success', "Nộp bài tập thành công! Điểm của bạn: {$score}/10");
    }

    public function review(Assignment $assignment)
    {
        /** @var User $user */
        $user = Auth::user();
        if (!$user->isStudent()) {
            abort(403);
        }

        $course = $assignment->course;
        if (!$course->students()->where('student_id', $user->id)->exists()) {
            abort(403, 'Bạn chưa tham gia khóa học này.');
        }

        $grade = Grade::where('student_id', $user->id)
            ->where('gradeable_type', 'App\\Models\\Assignment')
            ->where('gradeable_id', $assignment->id)
            ->firstOrFail();

        $details = json_decode($grade->submission_content ?? '[]', true) ?: [];

        return view('assignment.review', [
            'assignment' => $assignment,
            'course' => $course,
            'grade' => $grade,
            'details' => $details,
        ]);
    }

    private function normalizeQuestions(array $questions): array
    {
        $normalized = [];
        foreach ($questions as $question) {
            $correctOptions = $question['correct_options'] ?? [];
            if (!is_array($correctOptions)) {
                $correctOptions = [$correctOptions];
            }
            $correctOptions = array_values(array_unique(array_map('intval', $correctOptions)));

            $normalized[] = [
                'question_text' => $question['question_text'],
                'options' => array_values($question['options']),
                'type' => $question['type'] ?? 'single',
                'correct_options' => $correctOptions,
            ];
        }

        return $normalized;
    }

    // Teacher grades/overrides an assignment score
    public function grade(Request $request, Grade $grade)
    {
        /** @var User $user */
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
        /** @var User $user */
        $user = Auth::user();
        $course = $assignment->course;
        if (!$user->isTeacher() || $course->classroom->teacher_id !== $user->id) {
            abort(403);
        }

        $assignment->delete();

        return back()->with('success', 'Đã xóa bài tập trắc nghiệm thành công.');
    }
}
