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
        Schema::create('pricing_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('price');
            $table->text('description')->nullable();
            $table->json('features'); // Store features as JSON array
            $table->string('icon')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->string('plan_type')->default('non-masking'); // non-masking, masking, etc.
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->string('min_order_amount')->nullable()->default('500');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pricing_plans');
    }
};
