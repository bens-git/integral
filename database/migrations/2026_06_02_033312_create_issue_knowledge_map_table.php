<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('issue_knowledge_map', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('issue_id')->constrained('decision_issues')->cascadeOnDelete();
            $table->foreignUuid('knowledge_id')->constrained('knowledge_nodes')->cascadeOnDelete();
            $table->foreignUuid('added_by_id')->nullable()->constrained('participants')->nullOnDelete();
            $table->string('relevance')->default('supporting'); // supporting, contradicting, contextual, foundational
            $table->integer('weight')->default(1); // importance weight 1-10
            $table->text('context_notes')->nullable();
            $table->timestamps();

            $table->unique(['issue_id', 'knowledge_id']);
            $table->index('relevance');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('issue_knowledge_map');
    }
};