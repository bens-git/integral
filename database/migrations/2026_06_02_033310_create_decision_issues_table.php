<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('decision_issues', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('submission_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('node_id')->nullable();
            $table->text('framed_problem');
            $table->text('scope')->nullable();
            $table->text('success_criteria')->nullable();
            $table->text('constraints')->nullable();
            $table->integer('priority')->default(5); // 1=highest, 10=lowest
            $table->string('status')->default('draft'); // draft, framing, deliberation, consensus, decided, implemented, archived
            $table->string('decision_type')->nullable(); // policy, resource_allocation, design_approval, coordination, review
            $table->foreignUuid('facilitator_id')->nullable()->constrained('participants')->nullOnDelete();
            $table->timestamp('framing_completed_at')->nullable();
            $table->timestamp('deliberation_started_at')->nullable();
            $table->timestamp('consensus_reached_at')->nullable();
            $table->timestamp('implemented_at')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('priority');
            $table->index('node_id');
            $table->index('decision_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('decision_issues');
    }
};