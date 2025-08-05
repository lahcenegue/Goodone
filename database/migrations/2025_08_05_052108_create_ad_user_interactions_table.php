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
        Schema::create('ad_user_interactions', function (Blueprint $table) {
            $table->id();

            // Core relationships
            $table->unsignedBigInteger('ad_id')->comment('Reference to ads table');
            $table->unsignedBigInteger('user_id')->nullable()->comment('User who interacted (null for anonymous)');

            // Interaction details
            $table->enum('interaction_type', ['view', 'click', 'impression', 'conversion'])->comment('Type of interaction');
            $table->string('placement')->comment('Where the ad was shown');
            $table->string('device_type', 50)->nullable()->comment('mobile, tablet, web');

            // Session and context
            $table->string('session_id', 100)->nullable()->comment('User session identifier');
            $table->ipAddress('ip_address')->nullable()->comment('User IP address');
            $table->text('user_agent')->nullable()->comment('Browser user agent');
            $table->string('referrer_url')->nullable()->comment('Page where interaction occurred');

            // Geographic data
            $table->string('country', 100)->nullable()->comment('User country');
            $table->string('region', 100)->nullable()->comment('User region/state');
            $table->string('city', 100)->nullable()->comment('User city');

            // Interaction metadata
            $table->json('interaction_metadata')->nullable()->comment('Additional interaction data');
            $table->decimal('cost_amount', 8, 4)->default(0)->comment('Cost associated with this interaction');
            $table->timestamp('interaction_timestamp')->nullable()->comment('When interaction occurred');

            $table->timestamps();

            // Indexes for performance
            $table->index(['ad_id', 'interaction_type'], 'ad_interactions_ad_type_index');
            $table->index(['user_id', 'interaction_type'], 'ad_interactions_user_type_index');
            $table->index(['session_id'], 'ad_interactions_session_index');
            $table->index(['interaction_timestamp'], 'ad_interactions_timestamp_index');
            $table->index(['placement', 'interaction_type'], 'ad_interactions_placement_type_index');
            $table->index(['country', 'region', 'city'], 'ad_interactions_location_index');

            // Foreign key constraints
            $table->foreign('ad_id')->references('id')->on('ads')->onDelete('cascade');

            // Only add user foreign key if users table exists
            if (Schema::hasTable('users')) {
                $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ad_user_interactions');
    }
};
