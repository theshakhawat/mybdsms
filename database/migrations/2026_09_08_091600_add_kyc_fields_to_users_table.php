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
            $table->string('phone')->nullable()->after('email');
            $table->string('profile_image')->nullable()->after('password');
            $table->string('nid_front')->nullable()->after('profile_image');
            $table->string('nid_back')->nullable()->after('nid_front');
            $table->string('trade_license')->nullable()->after('nid_back');
            $table->enum('kyc_status', ['pending', 'approved', 'rejected'])->default('pending')->after('trade_license');
            $table->text('rejection_reason')->nullable()->after('kyc_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'phone',
                'profile_image',
                'nid_front',
                'nid_back',
                'trade_license',
                'kyc_status',
                'rejection_reason',
            ]);
        });
    }
};
