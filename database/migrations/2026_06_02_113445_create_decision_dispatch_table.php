<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('decision_dispatch', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('consensus_id')->constrained('consensus_models')->cascadeOnDelete();
            $table->foreignUuid('issue_id')->constrained('decision_issues')->cascadeOnDelete();
            $table->foreignUuid('node_id')->nullable();
            $table->string('target_system'); // cos, oad, itc, frs, external
            $table->string('action_type'); // create_project, allocate_resources, update_design, trigger_review, notify
            $table->text('payload_summary')->nullable();
            $table->string('status')->default('pending'); // pending, processing, completed, failed, cancelled
            $table->string('priority')->default('normal'); // low, normal, high, urgent
            $table->foreignUuid('dispatched_by_id')->nullable()->constrained('participants')->nullOnDelete();
            $table->foreignUuid('completed_by_id')->nullable()->constrained('participants')->nullOnDelete();
            $table->text('result_notes')->nullable();
            $table->text('error_message')->nullable();
            $table->integer('retry_count')->default(0);
            $table->timestamp('dispatched_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('next_retry_at')->nullable();
            $table->timestamps();

            $table->index('target_system');
            $table->index('status');
            $table->index('priority');
            $table->index('dispatched_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('decision_dispatch');
    }
};