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
        Schema::table('users', function (Blueprint $table) {
            // Add dedicated verification token fields
            $table->string('verification_token')->nullable()->after('reset_token_expiry');
            $table->timestamp('verification_token_expiry')->nullable()->after('verification_token');
            
            // Add index for performance on token lookups
            $table->index(['verification_token', 'verification_token_expiry'], 'users_verification_token_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop index first
            $table->dropIndex('users_verification_token_index');
            
            // Drop the columns
            $table->dropColumn(['verification_token', 'verification_token_expiry']);
        });
    }
};