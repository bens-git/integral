<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('knowledge_nodes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('node_id')->nullable();
            $table->foreignUuid('parent_id')->nullable(); // for hierarchical knowledge
            $table->foreignUuid('created_by_id')->nullable()->constrained('participants')->nullOnDelete();
            $table->string('type'); // evidence, precedent, research, standard, constraint, resource, ecological_data
            $table->string('title');
            $table->text('content');
            $table->text('summary')->nullable();
            $table->string('source')->nullable(); // internal, external, research, community
            $table->string('source_url')->nullable();
            $table->string('confidence_level')->default('medium'); // low, medium, high, verified
            $table->string('domain')->nullable(); // ecological, technical, social, economic, legal
            $table->boolean('is_verified')->default(false);
            $table->foreignUuid('verified_by_id')->nullable()->constrained('participants')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->string('license')->default('CC-BY-SA-4.0');
            $table->string('version')->default('1.0');
            $table->foreignUuid('supersedes_id')->nullable(); // references previous version
            $table->timestamps();

            $table->index('type');
            $table->index('domain');
            $table->index('confidence_level');
            $table->index('is_verified');
            $table->index('node_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('knowledge_nodes');
    }
};