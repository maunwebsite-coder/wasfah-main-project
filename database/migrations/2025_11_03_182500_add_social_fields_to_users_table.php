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
            $table->string('instagram_url')->nullable()->after('phone');
            $table->unsignedInteger('instagram_followers')->default(0)->after('instagram_url');
            $table->string('youtube_url')->nullable()->after('instagram_followers');
            $table->unsignedInteger('youtube_followers')->default(0)->after('youtube_url');
            $table->string('chef_specialty_area')->nullable()->after('youtube_followers');
            $table->text('chef_specialty_description')->nullable()->after('chef_specialty_area');
            $table->string('chef_status', 32)->default('needs_profile')->after('role');
            $table->timestamp('chef_approved_at')->nullable()->after('chef_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'instagram_url',
                'instagram_followers',
                'youtube_url',
                'youtube_followers',
                'chef_specialty_area',
                'chef_specialty_description',
                'chef_status',
                'chef_approved_at',
            ]);
        });
    }
};
