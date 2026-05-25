<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('grades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('classroom_id')->constrained('classrooms')->onDelete('cascade');
            $table->foreignId('course_id')->constrained('courses')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->unsignedBigInteger('gradeable_id');
            $table->string('gradeable_type'); // e.g. App\Models\Assignment or App\Models\Quiz
            $table->float('score')->nullable(); // nullable until graded by teacher (for Assignment)
            $table->text('submission_content')->nullable(); // student's submitted text/answers
            $table->dateTime('submitted_at')->nullable();
            $table->timestamps();

            // Indexing for quick lookups
            $table->index(['gradeable_id', 'gradeable_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grades');
    }
};
