<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customer_sessions', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('customer_id');
            $table->foreign('customer_id')->references('id')->on('users');
            $table->string('session_token');
            $table->string('device_type')->nullable(); // 'mobile', 'web', 'tablet'
            $table->string('device_name')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('location')->nullable(); // City, Country
            $table->timestamp('login_at');
            $table->timestamp('logout_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index(['customer_id', 'is_active']);
            $table->index(['customer_id', 'login_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_sessions');
    }
};