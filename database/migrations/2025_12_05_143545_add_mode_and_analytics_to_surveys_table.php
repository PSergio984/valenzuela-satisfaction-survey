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
        Schema::table('surveys', function (Blueprint $table) {
            $table->string('mode')->default('simple')->after('slug');
            $table->boolean('is_public')->default(true)->after('is_active');
            $table->unsignedBigInteger('views_count')->default(0)->after('thank_you_message');
            $table->unsignedBigInteger('starts_count')->default(0)->after('views_count');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('surveys', function (Blueprint $table) {
            $table->dropColumn(['mode', 'is_public', 'views_count', 'starts_count']);
        });
    }
};
