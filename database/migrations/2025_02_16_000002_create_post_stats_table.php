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
        Schema::create('post_stats', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('account_id')->constrained();
            $table->foreignUuid('post_id')->constrained()->onDelete('cascade');
            $table->string('url')->nullable();
            $table->string('platform')->nullable();
            $table->string('platform_id')->nullable();

            $table->string('status')->nullable();
            $table->text('http_response')->nullable();
            $table->timestamp('published_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('post_stats');
    }
};
