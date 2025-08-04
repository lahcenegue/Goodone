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
        Schema::table('notifications', function (Blueprint $table) {
            // Add new columns if they don't exist
            if (!Schema::hasColumn('notifications', 'is_new')) {
                $table->boolean('is_new')->default(true)->after('data');
            }
            
            if (!Schema::hasColumn('notifications', 'is_read')) {
                $table->boolean('is_read')->default(false)->after('is_new');
            }
            
            if (!Schema::hasColumn('notifications', 'seen_at')) {
                $table->timestamp('seen_at')->nullable()->after('is_read');
            }
            
            if (!Schema::hasColumn('notifications', 'read_at')) {
                $table->timestamp('read_at')->nullable()->after('seen_at');
            }
            
            // Add indexes for better performance
            $table->index(['user_id', 'is_new']);
            $table->index(['user_id', 'is_read']);
            $table->index(['user_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'is_new']);
            $table->dropIndex(['user_id', 'is_read']);
            $table->dropIndex(['user_id', 'created_at']);
            
            $table->dropColumn(['is_new', 'is_read', 'seen_at', 'read_at']);
        });
    }
};