<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use App\Enums\User\Theme;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('theme')->default(Theme::LIGHT);
            $table->foreignUuid('language_id')->constrained();
            $table->foreignUuid('current_workspace_id')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes('deleted_at');

            $table->unique(['email', 'deleted_at']);
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignUuid('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });

        Schema::create('plans', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('internal_id');
            $table->integer('price');
            $table->string('stripe_id')->nullable();
            $table->boolean('is_monthly');
            $table->integer('access_level');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('workspaces', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->foreignUuid('plan_id')->constrained();
            $table->string('stripe_id')->nullable()->index();
            $table->string('pm_type')->nullable();
            $table->string('pm_last_four', 4)->nullable();
            $table->timestamp('trial_ends_at')->nullable();

            $table->timestamps();
            $table->softDeletes('deleted_at');
        });

        Schema::create('user_workspace', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('user_id')->constrained();
            $table->foreignUuid('workspace_id')->constrained();
            $table->string('role');
            $table->timestamps();
        });

        Schema::create('invites', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('email');
            $table->string('role');
            $table->foreignUuid('workspace_id')->constrained();
            $table->timestamps();
            $table->unique(['email', 'workspace_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
