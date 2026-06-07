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
        Schema::create('submission_clusters', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('decision_issue_id')->nullable()->constrained('decision_issues')->nullOnDelete();
            $table->string('title')->nullable();
            $table->text('summary')->nullable();
            $table->json('centroid')->nullable();
            $table->integer('submissions_count')->default(0);
            $table->timestamps();

            $table->index('decision_issue_id');
            $table->index('created_at');
        });

        Schema::create('submission_cluster_members', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('cluster_id');
            $table->uuid('submission_id');
            $table->double('similarity')->nullable();
            $table->timestamps();

            $table->foreign('cluster_id')->references('id')->on('submission_clusters')->cascadeOnDelete();
            $table->foreign('submission_id')->references('id')->on('submissions')->cascadeOnDelete();

            $table->index(['cluster_id']);
            $table->index(['submission_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('submission_cluster_members');
        Schema::dropIfExists('submission_clusters');
    }
};
