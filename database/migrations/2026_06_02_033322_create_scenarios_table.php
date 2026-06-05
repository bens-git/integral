<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('scenarios', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('issue_id')->constrained('decision_issues')->cascadeOnDelete();
            $table->foreignUuid('created_by_id')->nullable()->constrained('participants')->nullOnDelete();
            $table->string('title');
            $table->text('description');
            $table->text('assumptions')->nullable();
            $table->text('methodology')->nullable();
            $table->decimal('viability_score', 5, 2)->nullable(); // 0.00 to 100.00
            $table->decimal('risk_score', 5, 2)->nullable(); // 0.00 to 100.00
            $table->decimal('impact_score', 5, 2)->nullable(); // 0.00 to 100.00
            $table->string('status')->default('draft'); // draft, modeling, complete, archived
            $table->foreignUuid('based_on_id')->nullable(); // references parent scenario
            $table->timestamps();

            $table->index('issue_id');
            $table->index('status');
            $table->index('viability_score');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scenarios');
    }
};