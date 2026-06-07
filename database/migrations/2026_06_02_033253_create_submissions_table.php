<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('submissions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('submitter_id')->constrained('participants')->cascadeOnDelete();
            // type of submission (proposal, objection, evidence, comment, signal)
            $table->enum('submission_type', ['proposal','objection','evidence','comment','signal'])->default('proposal');
            $table->foreignUuid('node_id')->nullable();
            $table->string('title');
            $table->text('description');
            $table->text('summary')->nullable();
            $table->longText('content')->nullable();
            $table->string('status')->default('draft'); // draft, submitted, validated, framed, active, accepted, rejected, implemented, archived
            $table->string('category')->nullable(); // policy, resource, design, coordination, review
            $table->string('priority')->default('normal'); // low, normal, high, urgent
            $table->string('scope')->nullable(); // local, regional, bioregional, global
            $table->uuid('version')->default('1');
            $table->foreignUuid('supersedes_id')->nullable(); // references previous proposal version
            $table->boolean('is_amendment')->default(false);
            $table->foreignUuid('amends_id')->nullable(); // references proposal being amended
            $table->timestamps();

            $table->index('status');
            $table->index('node_id');
            $table->index('submitter_id');
            $table->index('category');
            $table->index('priority');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('submissions');
    }
};