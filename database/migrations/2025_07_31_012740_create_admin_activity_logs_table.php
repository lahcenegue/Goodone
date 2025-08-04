<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admin_activity_logs', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('admin_id');
            $table->foreign('admin_id')->references('id')->on('admins');
            $table->bigInteger('customer_id');
            $table->foreign('customer_id')->references('id')->on('users');
            $table->string('action'); // 'blocked', 'unblocked', 'profile_updated', etc.
            $table->text('description');
            $table->json('old_values')->nullable(); // Store previous values
            $table->json('new_values')->nullable(); // Store new values
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();
            
            $table->index(['customer_id', 'created_at']);
            $table->index(['admin_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_activity_logs');
    }
};