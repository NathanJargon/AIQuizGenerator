<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quiz_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('position');
            $table->longText('question_text');
            $table->text('choice_a');
            $table->text('choice_b');
            $table->text('choice_c');
            $table->text('choice_d');
            $table->char('correct_answer', 1);
            $table->longText('explanation');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quiz_questions');
    }
};