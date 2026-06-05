<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('consensus_models', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('issue_id')->constrained('decision_issues')->cascadeOnDelete();
            $table->foreignUuid('node_id')->nullable();
            $table->string('method')->default('weighted_consensus'); // weighted_consensus, condorcet, ranked_choice, consent
            $table->decimal('consensus_score', 5, 2)->nullable(); // 0.00 to 100.00
            $table->string('outcome')->default('pending'); // pending, consensus_reached, consent, blocked, withdrawn
            $table->integer('threshold')->default(75); // percentage threshold for consensus
            $table->integer('total_participants')->default(0);
            $table->integer('total_votes')->default(0);
            $table->integer('votes_strong_support')->default(0);
            $table->integer('votes_support')->default(0);
            $table->integer('votes_neutral')->default(0);
            $table->integer('votes_concern')->default(0);
            $table->integer('votes_block')->default(0);
            $table->integer('blocking_objections')->default(0);
            $table->text('summary')->nullable();
            $table->text('rationale')->nullable();
            $table->foreignUuid('facilitator_id')->nullable()->constrained('participants')->nullOnDelete();
            $table->timestamp('voting_started_at')->nullable();
            $table->timestamp('voting_ended_at')->nullable();
            $table->timestamp('outcome_declared_at')->nullable();
            $table->timestamps();

            $table->index('issue_id');
            $table->index('outcome');
            $table->index('consensus_score');
            $table->index('method');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consensus_models');
    }
};