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
        Schema::table('ads', function (Blueprint $table) {
            // ===============================
            // ADMIN CONTROL FEATURES
            // ===============================

            // Priority and management
            $table->integer('admin_priority')->default(2)->after('display_order')->comment('1=Low, 2=Normal, 3=High, 4=Urgent');
            $table->boolean('auto_pause_enabled')->default(false)->after('admin_priority')->comment('Enable automatic pause for poor performance');
            $table->decimal('min_ctr_threshold', 5, 2)->nullable()->after('auto_pause_enabled')->comment('Minimum CTR % before auto-pause');

            // Budget management
            $table->decimal('max_daily_budget', 10, 2)->nullable()->after('min_ctr_threshold')->comment('Maximum daily spend limit');
            $table->decimal('daily_spend', 10, 2)->default(0)->after('max_daily_budget')->comment('Current daily spend amount');

            // Admin workflow
            $table->enum('approval_status', ['pending', 'approved', 'rejected'])->default('approved')->after('daily_spend')->comment('Admin approval status');
            $table->decimal('performance_score', 5, 2)->default(0)->after('approval_status')->comment('Calculated performance score');

            // ===============================
            // ADVANCED TARGETING
            // ===============================

            // Geographic targeting
            $table->json('target_regions')->nullable()->after('performance_score')->comment('Array of target regions/cities');
            $table->json('exclude_regions')->nullable()->after('target_regions')->comment('Array of excluded regions/cities');

            // User type targeting
            $table->json('target_user_types')->nullable()->after('exclude_regions')->comment('Array: customer, worker, both');
            $table->json('exclude_user_types')->nullable()->after('target_user_types')->comment('Array of excluded user types');

            // Activity level targeting
            $table->json('target_activity_levels')->nullable()->after('exclude_user_types')->comment('Array: new, low, medium, high, vip');

            // Order history targeting
            $table->json('target_order_history')->nullable()->after('target_activity_levels')->comment('Array: no_orders, first_time, repeat_customer, high_value, low_value');

            // ===============================
            // ADMIN MANAGEMENT
            // ===============================

            // Admin tracking
            $table->unsignedBigInteger('created_by_admin')->nullable()->after('target_order_history')->comment('Admin who created this ad');
            $table->unsignedBigInteger('last_modified_by_admin')->nullable()->after('created_by_admin')->comment('Admin who last modified this ad');
            $table->text('admin_notes')->nullable()->after('last_modified_by_admin')->comment('Internal admin notes');

            // ===============================
            // SCHEDULING ENHANCEMENTS
            // ===============================

            // Advanced scheduling
            $table->string('schedule_template')->nullable()->after('admin_notes')->comment('Template: always_on, business_hours, weekends, custom');
            $table->json('recurring_schedule')->nullable()->after('schedule_template')->comment('Recurring schedule configuration');
            $table->string('timezone', 50)->default('America/Toronto')->after('recurring_schedule')->comment('Timezone for scheduling');

            // ===============================
            // INDEXES FOR PERFORMANCE
            // ===============================

            // Performance indexes
            $table->index(['admin_priority', 'is_active'], 'ads_priority_active_index');
            $table->index(['approval_status', 'is_active'], 'ads_approval_active_index');
            $table->index(['auto_pause_enabled', 'performance_score'], 'ads_auto_pause_performance_index');
            $table->index(['max_daily_budget', 'daily_spend'], 'ads_budget_index');
            $table->index(['created_by_admin'], 'ads_created_by_admin_index');
            $table->index(['last_modified_by_admin'], 'ads_modified_by_admin_index');

            // Targeting indexes
            $table->index(['target_user_types'], 'ads_target_users_index');
            $table->index(['schedule_template'], 'ads_schedule_template_index');
        });

        // ===============================
        // ADD FOREIGN KEY CONSTRAINTS
        // ===============================

        // Only add foreign keys if the admins table exists
        if (Schema::hasTable('admins')) {
            Schema::table('ads', function (Blueprint $table) {
                $table->foreign('created_by_admin')
                    ->references('id')
                    ->on('admins')
                    ->onDelete('set null')
                    ->name('ads_created_by_admin_foreign');

                $table->foreign('last_modified_by_admin')
                    ->references('id')
                    ->on('admins')
                    ->onDelete('set null')
                    ->name('ads_modified_by_admin_foreign');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ads', function (Blueprint $table) {
            // Drop foreign keys first if they exist
            if (Schema::hasTable('admins')) {
                $table->dropForeign('ads_created_by_admin_foreign');
                $table->dropForeign('ads_modified_by_admin_foreign');
            }

            // Drop indexes
            $table->dropIndex('ads_priority_active_index');
            $table->dropIndex('ads_approval_active_index');
            $table->dropIndex('ads_auto_pause_performance_index');
            $table->dropIndex('ads_budget_index');
            $table->dropIndex('ads_created_by_admin_index');
            $table->dropIndex('ads_modified_by_admin_index');
            $table->dropIndex('ads_target_users_index');
            $table->dropIndex('ads_schedule_template_index');

            // Drop columns in reverse order
            $table->dropColumn([
                'timezone',
                'recurring_schedule',
                'schedule_template',
                'admin_notes',
                'last_modified_by_admin',
                'created_by_admin',
                'target_order_history',
                'target_activity_levels',
                'exclude_user_types',
                'target_user_types',
                'exclude_regions',
                'target_regions',
                'performance_score',
                'approval_status',
                'daily_spend',
                'max_daily_budget',
                'min_ctr_threshold',
                'auto_pause_enabled',
                'admin_priority'
            ]);
        });
    }
};
