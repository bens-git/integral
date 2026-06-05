<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('deliberation_messages', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('thread_id')->constrained('deliberation_threads')->cascadeOnDelete();
            $table->foreignUuid('participant_id')->constrained('participants')->cascadeOnDelete();
            $table->foreignUuid('parent_id')->nullable(); // for threaded replies
            $table->text('message');
            $table->string('stance')->default('neutral'); // support, concern, objection, question, neutral, suggestion
            $table->string('message_type')->default('text'); // text, evidence, amendment, procedural
            $table->boolean('is_edited')->default(false);
            $table->timestamp('edited_at')->nullable();
            $table->integer('upvotes')->default(0);
            $table->integer('downvotes')->default(0);
            $table->boolean('is_resolved')->default(false);
            $table->foreignUuid('resolved_by_id')->nullable()->constrained('participants')->nullOnDelete();
            $table->timestamps();

            $table->index('thread_id');
            $table->index('participant_id');
            $table->index('stance');
            $table->index('message_type');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deliberation_messages');
    }
};