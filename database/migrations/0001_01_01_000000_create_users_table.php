<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('districts')) {
            Schema::create('districts', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name', 100)->index();
                $table->string('city_code', 20);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('report_categories')) {
            Schema::create('report_categories', function (Blueprint $table) {
                $table->increments('id');
                $table->string('category_name', 50);
                $table->unsignedTinyInteger('severity_level')->default(1);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('district_id')->nullable()->index();
                $table->string('name', 150);
                $table->string('email', 100)->unique();
                $table->string('phone_number', 20);
                $table->string('password', 255);
                $table->enum('role', ['warga', 'petugas', 'admin'])->default('warga')->index();
                $table->string('profile_photo', 255)->nullable();
                $table->timestamps();

                $table->foreign('district_id')->references('id')->on('districts')->nullOnDelete();
            });
        }

        if (! Schema::hasTable('reports')) {
            Schema::create('reports', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('user_id')->index();
                $table->unsignedInteger('category_id')->index();
                $table->unsignedInteger('district_id')->index();
                $table->string('ticket_code', 30)->unique();
                $table->text('description');
                $table->string('photo_url', 255);
                $table->decimal('latitude', 10, 8)->index();
                $table->decimal('longitude', 11, 8);
                $table->boolean('ai_validation')->default(false);
                $table->decimal('ai_confidence', 5, 2)->default(0.00);
                $table->enum('status', ['submitted', 'verified', 'in_progress', 'resolved', 'rejected'])->default('submitted')->index();
                $table->timestamps();

                $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
                $table->foreign('category_id')->references('id')->on('report_categories')->cascadeOnDelete();
                $table->foreign('district_id')->references('id')->on('districts')->cascadeOnDelete();
            });
        }

        if (! Schema::hasTable('work_orders')) {
            Schema::create('work_orders', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('report_id')->unique();
                $table->unsignedInteger('officer_id')->index();
                $table->string('spk_number', 50)->unique();
                $table->date('target_sla');
                $table->enum('work_status', ['assigned', 'in_progress', 'completed'])->default('assigned')->index();
                $table->string('after_photo_url', 255)->nullable();
                $table->text('repair_notes')->nullable();
                $table->dateTime('resolved_at')->nullable();
                $table->timestamps();

                $table->foreign('report_id')->references('id')->on('reports')->cascadeOnDelete();
                $table->foreign('officer_id')->references('id')->on('users')->cascadeOnDelete();
            });
        }

        if (! Schema::hasTable('ratings')) {
            Schema::create('ratings', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('report_id')->unique();
                $table->unsignedInteger('user_id')->index();
                $table->unsignedTinyInteger('score');
                $table->text('review_text')->nullable();
                $table->timestamp('created_at')->useCurrent();

                $table->foreign('report_id')->references('id')->on('reports')->cascadeOnDelete();
                $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            });
        }

        if (! Schema::hasTable('audit_logs')) {
            Schema::create('audit_logs', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('report_id')->index();
                $table->unsignedInteger('user_id')->index();
                $table->string('action_name', 100);
                $table->string('previous_status', 50)->nullable();
                $table->string('new_status', 50);
                $table->timestamp('created_at')->useCurrent();

                $table->foreign('report_id')->references('id')->on('reports')->cascadeOnDelete();
                $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('ratings');
        Schema::dropIfExists('work_orders');
        Schema::dropIfExists('reports');
        Schema::dropIfExists('users');
        Schema::dropIfExists('report_categories');
        Schema::dropIfExists('districts');
    }
};
