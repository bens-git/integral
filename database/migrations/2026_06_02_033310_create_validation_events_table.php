<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('validation_events', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('proposal_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('validator_id')->constrained('participants')->cascadeOnDelete();
            $table->foreignUuid('node_id')->nullable();
            $table->string('result'); // valid, invalid, needs_revision, pending
            $table->text('notes')->nullable();
            $table->string('validation_type')->default('general'); // technical, ecological, social, legal, resource
            $table->boolean('is_blocking')->default(false);
            $table->foreignUuid('supersedes_id')->nullable(); // references previous validation
            $table->timestamps();

            $table->index('result');
            $table->index('validation_type');
            $table->index('is_blocking');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('validation_events');
    }
};