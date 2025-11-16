<?php

use App\Models\Workshop;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Workshop::withoutTimestamps(function () {
            Workshop::query()
                ->whereNull('slug')
                ->orWhere('slug', '')
                ->chunkById(50, function ($workshops) {
                    foreach ($workshops as $workshop) {
                        if (blank($workshop->title)) {
                            continue;
                        }

                        $workshop->slug = $workshop->generateSlug();
                        $workshop->saveQuietly();
                    }
                });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Intentionally left blank - slugs should remain populated.
    }
};
