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
        Schema::table('pricing_plans', function (Blueprint $table) {
            if (!Schema::hasColumn('pricing_plans', 'button_link')) {
                $table->string('button_link')->nullable()->after('description');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pricing_plans', function (Blueprint $table) {
            if (Schema::hasColumn('pricing_plans', 'button_link')) {
                $table->dropColumn('button_link');
            }
        });
    }
};
