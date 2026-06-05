<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('objection_map', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('consensus_id')->constrained('consensus_models')->cascadeOnDelete();
            $table->foreignUuid('participant_id')->constrained('participants')->cascadeOnDelete();
            $table->foreignUuid('node_id')->nullable();
            $table->decimal('objection_strength', 3, 2)->default(1.00); // 0.00 to 5.00
            $table->string('objection_type')->default('principled'); // principled, practical, procedural, technical
            $table->text('reason');
            $table->text('proposed_resolution')->nullable();
            $table->string('status')->default('open'); // open, addressed, resolved, withdrawn, upheld
            $table->boolean('is_blocking')->default(false);
            $table->foreignUuid('addressed_by_id')->nullable()->constrained('participants')->nullOnDelete();
            $table->text('resolution_notes')->nullable();
            $table->timestamp('addressed_at')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();

            $table->index('consensus_id');
            $table->index('status');
            $table->index('is_blocking');
            $table->index('objection_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('objection_map');
    }
};