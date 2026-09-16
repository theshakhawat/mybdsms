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
        Schema::create('stats_section', function (Blueprint $table) {
            $table->id();
            $table->string('clients_count')->default('500');
            $table->string('clients_label')->default('Happy Clients');
            $table->string('sms_count')->default('10');
            $table->string('sms_label')->default('Million+ SMS');
            $table->string('delivery_count')->default('99');
            $table->string('delivery_label')->default('% Delivery Rate');
            $table->string('years_count')->default('5');
            $table->string('years_label')->default('Years Experience');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stats_section');
    }
};
