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
            if (!Schema::hasColumn('users', 'sms_balance')) {
                $table->bigInteger('sms_balance')->default(0)->after('status');
            }
        });

        Schema::table('site_settings', function (Blueprint $table) {
            if (!Schema::hasColumn('site_settings', 'bank_name')) {
                $table->string('bank_name')->nullable()->after('company_hours');
                $table->string('bank_account_name')->nullable()->after('bank_name');
                $table->string('bank_account_number')->nullable()->after('bank_account_name');
                $table->string('bank_branch')->nullable()->after('bank_account_number');
                $table->string('bank_routing_number')->nullable()->after('bank_branch');
                $table->text('bank_instructions')->nullable()->after('bank_routing_number');
                $table->text('office_payment_instructions')->nullable()->after('bank_instructions');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'sms_balance')) {
                $table->dropColumn('sms_balance');
            }
        });

        Schema::table('site_settings', function (Blueprint $table) {
            $columns = [
                'bank_name',
                'bank_account_name',
                'bank_account_number',
                'bank_branch',
                'bank_routing_number',
                'bank_instructions',
                'office_payment_instructions',
            ];
            foreach ($columns as $column) {
                if (Schema::hasColumn('site_settings', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};

