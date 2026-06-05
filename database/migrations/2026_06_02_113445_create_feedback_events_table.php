<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feedback_events', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('issue_id')->constrained('decision_issues')->cascadeOnDelete();
            $table->foreignUuid('participant_id')->nullable()->constrained('participants')->nullOnDelete();
            $table->foreignUuid('node_id')->nullable();
            $table->string('source'); // frs, cos, oad, itc, community, external
            $table->string('feedback_type'); // performance, impact, fairness, efficiency, ecological, social
            $table->text('title');
            $table->text('description');
            $table->decimal('impact_score', 5, 2)->nullable(); // -100.00 to 100.00
            $table->decimal('urgency_score', 5, 2)->nullable(); // 0.00 to 100.00
            $table->string('status')->default('new'); // new, reviewing, addressed, closed
            $table->boolean('triggers_review')->default(false);
            $table->foreignUuid('reviewed_by_id')->nullable()->constrained('participants')->nullOnDelete();
            $table->text('review_notes')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();

            $table->index('issue_id');
            $table->index('source');
            $table->index('feedback_type');
            $table->index('status');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feedback_events');
    }
};