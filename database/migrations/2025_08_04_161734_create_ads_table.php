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
        Schema::create('ads', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('image'); // filename stored in storage/app/public/ads/
            $table->enum('ad_type', ['internal', 'external']);
            $table->enum('placement', ['home_banner', 'service_list', 'service_detail', 'profile']);
            $table->string('target_url')->nullable(); // for external ads
            $table->boolean('is_active')->default(true);
            $table->integer('display_order')->default(0); // for sorting ads
            $table->datetime('start_date')->nullable(); // ad scheduling
            $table->datetime('end_date')->nullable();   // ad scheduling
            $table->integer('click_count')->default(0); // analytics
            $table->integer('view_count')->default(0);  // analytics
            $table->timestamps();

            // Indexes for better performance
            $table->index(['is_active', 'placement']);
            $table->index(['ad_type', 'is_active']);
            $table->index(['start_date', 'end_date']);
            $table->index('display_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ads');
    }
};