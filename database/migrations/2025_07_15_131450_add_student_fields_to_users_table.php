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
            $table->string('student_id')->nullable()->unique()->after('email');
            $table->string('phone')->nullable()->after('student_id');
            $table->date('date_of_birth')->nullable()->after('phone');
            $table->enum('gender', ['male', 'female', 'other'])->nullable()->after('date_of_birth');
            $table->text('address')->nullable()->after('gender');
            $table->string('guardian_name')->nullable()->after('address');
            $table->string('guardian_phone')->nullable()->after('guardian_name');
            $table->string('course')->nullable()->after('guardian_phone');
            $table->string('level')->nullable()->after('course');
            $table->string('department')->nullable()->after('level');
            $table->date('enrollment_date')->nullable()->after('department');
            $table->enum('status', ['active', 'inactive', 'suspended', 'graduated'])->default('active')->after('enrollment_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'student_id',
                'phone',
                'date_of_birth',
                'gender',
                'address',
                'guardian_name',
                'guardian_phone',
                'course',
                'level',
                'department',
                'enrollment_date',
                'status'
            ]);
        });
    }
};
