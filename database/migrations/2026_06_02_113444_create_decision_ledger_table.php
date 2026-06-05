<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('decision_ledger', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('issue_id')->constrained('decision_issues')->cascadeOnDelete();
            $table->foreignUuid('consensus_id')->nullable()->constrained('consensus_models')->nullOnDelete();
            $table->foreignUuid('node_id')->nullable();
            $table->foreignUuid('participant_id')->nullable()->constrained('participants')->nullOnDelete();
            $table->string('event_type'); // created, framed, deliberation_started, vote_started, consensus_reached, blocked, implemented, revised, archived
            $table->text('title');
            $table->text('description')->nullable();
            $table->string('hash')->nullable(); // SHA256 hash for integrity
            $table->foreignUuid('previous_hash_id')->nullable(); // references previous ledger entry hash
            $table->string('signature')->nullable(); // cryptographic signature
            $table->boolean('is_verified')->default(false);
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();

            $table->index('issue_id');
            $table->index('event_type');
            $table->index('hash');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('decision_ledger');
    }
};