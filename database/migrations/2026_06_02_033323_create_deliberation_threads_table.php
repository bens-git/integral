<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('deliberation_threads', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('issue_id')->constrained('decision_issues')->cascadeOnDelete();
            $table->foreignUuid('parent_id')->nullable(); // for nested threads
            $table->foreignUuid('created_by_id')->nullable()->constrained('participants')->nullOnDelete();
            $table->string('title')->nullable();
            $table->text('topic')->nullable();
            $table->string('status')->default('open'); // open, closed, archived
            $table->boolean('is_pinned')->default(false);
            $table->boolean('is_locked')->default(false);
            $table->integer('message_count')->default(0);
            $table->timestamp('last_activity_at')->nullable();
            $table->timestamps();

            $table->index('issue_id');
            $table->index('status');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deliberation_threads');
    }
};