<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('workshop_views')) {
            Schema::create('workshop_views', function (Blueprint $table) {
                $table->id();
                $table->foreignId('workshop_id')->constrained()->cascadeOnDelete();
                $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
                $table->string('ip_address', 45);
                $table->string('user_agent')->nullable();
                $table->timestamp('viewed_at')->useCurrent();
                $table->timestamps();

                $table->unique(['workshop_id', 'ip_address'], 'workshop_views_unique_visit');
                $table->index(['workshop_id', 'ip_address'], 'workshop_views_workshop_ip_index');
            });

            return;
        }

        Schema::table('workshop_views', function (Blueprint $table) {
            if (! Schema::hasColumn('workshop_views', 'user_id')) {
                $table->foreignId('user_id')
                    ->nullable()
                    ->after('workshop_id')
                    ->constrained()
                    ->nullOnDelete();
            }

            if (! Schema::hasColumn('workshop_views', 'user_agent')) {
                $table->string('user_agent')->nullable()->after('ip_address');
            }

            if (! Schema::hasColumn('workshop_views', 'viewed_at')) {
                $table->timestamp('viewed_at')->useCurrent()->after('user_agent');
            }

            if (! Schema::hasColumn('workshop_views', 'created_at')) {
                $table->timestamps();
            }
        });

        $this->ensureIndexes();
    }

    protected function ensureIndexes(): void
    {
        $indexes = collect(DB::select("SHOW INDEX FROM `workshop_views`"))
            ->groupBy('Key_name');

        if (! $indexes->has('workshop_views_unique_visit')) {
            Schema::table('workshop_views', function (Blueprint $table) {
                $table->unique(['workshop_id', 'ip_address'], 'workshop_views_unique_visit');
            });
        }

        if (! $indexes->has('workshop_views_workshop_ip_index')) {
            Schema::table('workshop_views', function (Blueprint $table) {
                $table->index(['workshop_id', 'ip_address'], 'workshop_views_workshop_ip_index');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
