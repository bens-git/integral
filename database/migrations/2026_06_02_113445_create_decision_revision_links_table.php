<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('decision_revision_links', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('prior_decision_id')->constrained('decision_ledger')->cascadeOnDelete();
            $table->foreignUuid('revised_decision_id')->constrained('decision_ledger')->cascadeOnDelete();
            $table->foreignUuid('participant_id')->nullable()->constrained('participants')->nullOnDelete();
            $table->string('revision_type'); // amendment, correction, override, reversal, refinement
            $table->text('reason');
            $table->text('change_summary')->nullable();
            $table->boolean('is_major_revision')->default(false);
            $table->foreignUuid('approved_by_id')->nullable()->constrained('participants')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();

            $table->index('prior_decision_id');
            $table->index('revised_decision_id');
            $table->index('revision_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('decision_revision_links');
    }
};