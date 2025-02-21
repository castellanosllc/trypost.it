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
        Schema::create('accounts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('workspace_id')->constrained();
            $table->foreignUuid('space_id')->constrained();
            $table->string('platform');
            $table->string('platform_id')->unique();
            $table->string('name')->nullable();
            $table->string('username')->nullable();
            $table->string('photo', 1024)->nullable();
            $table->text('access_token');
            $table->text('refresh_token')->nullable();
            $table->integer('expires_in')->nullable();
            $table->string('status');
            $table->boolean('is_verified')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};
