<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('submission_clusters', function (Blueprint $table) {
            $table->json('keywords')->nullable();
            $table->float('confidence')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('submission_clusters', function (Blueprint $table) {
            $table->dropColumn(['keywords', 'confidence']);
        });
    }
};
