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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('order_id')->nullable()->constrained('orders')->onDelete('set null');
            $table->foreignId('package_id')->nullable()->constrained('pricing_plans')->onDelete('set null');
            $table->string('package_name');
            $table->string('plan_type')->nullable(); // masking, non-masking, custom
            $table->decimal('price_per_sms', 10, 4)->default(0);
            $table->bigInteger('sms_count')->default(0);
            $table->decimal('amount', 12, 2)->default(0);
            $table->decimal('paid_amount', 12, 2)->default(0);
            $table->string('payment_method')->default('bank'); // nagad, bank, office_cash, other
            $table->enum('status', ['unpaid', 'paid', 'cancelled'])->default('unpaid');
            $table->date('due_date')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->string('sender_bank_name')->nullable();
            $table->string('sender_account')->nullable();
            $table->string('trx_id')->nullable()->index();
            $table->string('slip_image')->nullable();
            $table->text('customer_notes')->nullable();
            $table->text('admin_notes')->nullable();
            $table->string('created_by')->default('user'); // user, admin
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};

