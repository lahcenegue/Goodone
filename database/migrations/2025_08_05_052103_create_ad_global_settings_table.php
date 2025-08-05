<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ad_global_settings', function (Blueprint $table) {
            $table->id();

            // Global Ad Controls
            $table->boolean('ads_enabled')->default(true)->comment('Global ad system enable/disable');
            $table->integer('max_ads_per_user_session')->default(10)->comment('Maximum ads shown per user session');
            $table->integer('ad_frequency_cap_minutes')->default(60)->comment('Minutes between same ad shows to same user');

            // Global Targeting Defaults
            $table->json('default_target_regions')->nullable()->comment('Default regions for new ads');
            $table->json('default_target_user_types')->nullable()->comment('Default user types for new ads');
            $table->boolean('require_admin_approval')->default(false)->comment('Require admin approval for all new ads');

            // Performance Defaults
            $table->decimal('default_min_ctr_threshold', 5, 2)->default(1.0)->comment('Default minimum CTR threshold');
            $table->decimal('default_max_daily_budget', 10, 2)->nullable()->comment('Default maximum daily budget');
            $table->boolean('auto_pause_poor_performers')->default(true)->comment('Auto-pause ads with poor performance');

            // Display Controls
            $table->integer('ads_per_placement_limit')->default(3)->comment('Maximum ads per placement');
            $table->boolean('show_ads_to_new_users')->default(true)->comment('Show ads to users registered < 7 days');
            $table->boolean('show_ads_to_inactive_users')->default(false)->comment('Show ads to inactive users');

            // Revenue & Analytics
            $table->decimal('cost_per_view', 8, 4)->default(0.01)->comment('Cost charged per ad view');
            $table->decimal('cost_per_click', 8, 4)->default(0.10)->comment('Cost charged per ad click');
            $table->boolean('track_user_behavior')->default(true)->comment('Enable detailed user behavior tracking');

            // Admin Controls
            $table->unsignedBigInteger('last_updated_by_admin')->nullable()->comment('Admin who last updated settings');
            $table->timestamp('last_settings_update')->nullable()->comment('When settings were last updated');
            $table->text('settings_notes')->nullable()->comment('Admin notes about current settings');

            $table->timestamps();

            // Indexes
            $table->index(['ads_enabled'], 'ad_settings_enabled_index');
            $table->index(['last_updated_by_admin'], 'ad_settings_admin_index');
        });

        // Insert default settings
        DB::table('ad_global_settings')->insert([
            'ads_enabled' => true,
            'max_ads_per_user_session' => 10,
            'ad_frequency_cap_minutes' => 60,
            'default_target_user_types' => json_encode(['both']),
            'require_admin_approval' => false,
            'default_min_ctr_threshold' => 1.0,
            'auto_pause_poor_performers' => true,
            'ads_per_placement_limit' => 3,
            'show_ads_to_new_users' => true,
            'show_ads_to_inactive_users' => false,
            'cost_per_view' => 0.01,
            'cost_per_click' => 0.10,
            'track_user_behavior' => true,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Add foreign key if admins table exists
        if (Schema::hasTable('admins')) {
            Schema::table('ad_global_settings', function (Blueprint $table) {
                $table->foreign('last_updated_by_admin')
                    ->references('id')
                    ->on('admins')
                    ->onDelete('set null');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ad_global_settings');
    }
};
