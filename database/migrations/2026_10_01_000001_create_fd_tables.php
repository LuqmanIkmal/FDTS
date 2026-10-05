<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Schema converted from the Oracle FD schema
 * (BANK, STAFF, FIXEDDEPOSITRECORD, FREEFD, PLEDGEFD, FIXEDDEPOSITTRANSACTION).
 * BLOB columns (staff picture, FD certificate) are stored as files on disk;
 * the columns hold the file path.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('banks', function (Blueprint $table) {
            $table->id('bank_id');
            $table->string('bank_name', 100)->unique();
            $table->string('bank_phone', 20)->nullable();
            $table->string('bank_address', 200)->nullable();
        });

        Schema::create('staff', function (Blueprint $table) {
            $table->id('staff_id');
            $table->string('staff_id_prefix', 20);
            $table->string('staff_name', 100);
            $table->string('staff_phone', 20);
            $table->string('staff_address', 200);
            $table->string('staff_email', 150)->unique();
            $table->string('password', 255);
            $table->string('staff_picture', 255)->nullable();
            $table->string('staff_role', 150);
            $table->string('staff_status', 50)->default('Active');
            $table->string('reason', 255)->nullable();
            $table->foreignId('manager_id')->nullable()
                ->constrained('staff', 'staff_id')->nullOnDelete();
        });

        Schema::create('fixed_deposit_records', function (Blueprint $table) {
            $table->id('fd_id');
            $table->string('acc_number', 30);
            $table->string('referral_number', 50)->nullable();
            $table->decimal('deposit_amount', 15, 2);
            $table->decimal('interest_rate', 5, 3);
            $table->date('start_date');
            $table->unsignedInteger('tenure');
            $table->date('maturity_date');
            $table->string('fd_cert', 255)->nullable();
            $table->string('cert_no', 30)->nullable()->unique();
            $table->string('fd_type', 30);
            $table->string('status', 30)->default('PENDING');
            $table->foreignId('bank_id')->constrained('banks', 'bank_id');
            $table->char('reminder_maturity', 1)->default('N');
            $table->char('reminder_incomplete', 1)->default('N');
            $table->foreignId('previous_fd_id')->nullable()
                ->constrained('fixed_deposit_records', 'fd_id')->nullOnDelete();
            $table->decimal('remaining_balance', 15, 2)->nullable();
            $table->decimal('total_withdrawn', 15, 2)->default(0);
        });

        Schema::create('free_fds', function (Blueprint $table) {
            $table->foreignId('fd_id')->primary()
                ->constrained('fixed_deposit_records', 'fd_id')->cascadeOnDelete();
            $table->char('auto_renewal_status', 1);
            $table->string('withdrawable_status', 10)->nullable();
        });

        Schema::create('pledge_fds', function (Blueprint $table) {
            $table->foreignId('fd_id')->primary()
                ->constrained('fixed_deposit_records', 'fd_id')->cascadeOnDelete();
            $table->char('collateral_status', 1)->default('N');
            $table->decimal('pledge_value', 15, 2)->nullable();
        });

        Schema::create('fixed_deposit_transactions', function (Blueprint $table) {
            $table->id('transaction_id');
            $table->foreignId('fd_id')->constrained('fixed_deposit_records', 'fd_id')->cascadeOnDelete();
            // NULL staff_id = performed by the system (auto-renewal)
            $table->foreignId('staff_id')->nullable()->constrained('staff', 'staff_id')->nullOnDelete();
            $table->timestamp('transaction_date')->useCurrent();
            $table->decimal('calc_total_profit', 15, 2)->nullable();
            $table->string('transaction_type', 30)->nullable();
            $table->decimal('withdraw_amount', 15, 2)->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fixed_deposit_transactions');
        Schema::dropIfExists('pledge_fds');
        Schema::dropIfExists('free_fds');
        Schema::dropIfExists('fixed_deposit_records');
        Schema::dropIfExists('staff');
        Schema::dropIfExists('banks');
    }
};
