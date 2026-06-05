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
        Schema::create('system_signals', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('node_id')->nullable();
            $table->foreignUuid('proposal_id')->nullable();
            $table->foreignUuid('issue_id')->nullable();
            $table->string('source'); // frs, cos, oad, itc, external_api, manual
            $table->string('signal_type'); // alert, recommendation, constraint_violation, resource_warning, fairness_issue
            $table->string('severity')->default('info'); // info, warning, critical
            $table->text('title');
            $table->text('description');
            $table->string('target_system')->nullable(); // cds, oad, cos, itc, frs
            $table->string('action_required')->default('review'); // review, immediate_action, schedule_review, acknowledge
            $table->foreignUuid('resolved_by_id')->nullable(); // participant who resolved
            $table->timestamp('resolved_at')->nullable();
            $table->text('resolution_notes')->nullable();
            $table->timestamps();

            $table->index('source');
            $table->index('signal_type');
            $table->index('severity');
            $table->index('node_id');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('system_signals');
    }
};