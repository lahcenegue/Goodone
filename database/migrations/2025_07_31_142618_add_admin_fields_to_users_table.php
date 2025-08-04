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
            // Check and add columns only if they don't exist
            if (!Schema::hasColumn('users', 'verified')) {
                $table->boolean('verified')->default(false)->after('email_verified_at');
            }
            if (!Schema::hasColumn('users', 'blocked')) {
                $table->boolean('blocked')->default(false)->after('picture');
            }
            if (!Schema::hasColumn('users', 'active')) {
                $table->boolean('active')->default(true)->after('picture');
            }
            if (!Schema::hasColumn('users', 'city')) {
                $table->string('city')->nullable()->after('picture');
            }
            if (!Schema::hasColumn('users', 'country')) {
                $table->string('country')->nullable()->after('picture');
            }
            if (!Schema::hasColumn('users', 'location')) {
                $table->text('location')->nullable()->after('picture');
            }
        });

        // Add indexes safely
        try {
            Schema::table('users', function (Blueprint $table) {
                $table->index(['type', 'active']);
                $table->index(['type', 'blocked']);
                $table->index(['type', 'verified']);
            });
        } catch (\Exception $e) {
            // Indexes might already exist, ignore error
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Remove indexes safely
            try {
                $table->dropIndex(['type', 'active']);
                $table->dropIndex(['type', 'blocked']);
                $table->dropIndex(['type', 'verified']);
            } catch (\Exception $e) {
                // Ignore if indexes don't exist
            }

            // Remove columns only if they exist
            if (Schema::hasColumn('users', 'location')) {
                $table->dropColumn('location');
            }
            if (Schema::hasColumn('users', 'country')) {
                $table->dropColumn('country');
            }
            if (Schema::hasColumn('users', 'city')) {
                $table->dropColumn('city');
            }
            if (Schema::hasColumn('users', 'active')) {
                $table->dropColumn('active');
            }
            if (Schema::hasColumn('users', 'blocked')) {
                $table->dropColumn('blocked');
            }
            if (Schema::hasColumn('users', 'verified')) {
                $table->dropColumn('verified');
            }
        });
    }
};
