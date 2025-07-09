<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccountDeletionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('account_deletions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('user_email');
            $table->enum('user_type', ['customer', 'worker']);
            $table->string('deletion_reason');
            $table->text('additional_feedback')->nullable();
            $table->timestamp('deleted_at');
            $table->timestamps();
            
            // Index for faster queries
            $table->index(['user_type', 'deleted_at']);
            $table->index('deleted_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('account_deletions');
    }
}