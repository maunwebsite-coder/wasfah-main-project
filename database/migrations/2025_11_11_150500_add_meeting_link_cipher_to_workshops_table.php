<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('workshops', function (Blueprint $table) {
            if (!Schema::hasColumn('workshops', 'meeting_link_cipher')) {
                $table->longText('meeting_link_cipher')->nullable()->after('meeting_link');
            }
        });

        DB::table('workshops')
            ->whereNotNull('meeting_link')
            ->orderBy('id')
            ->chunkById(100, function ($workshops) {
                foreach ($workshops as $workshop) {
                    $plain = $workshop->meeting_link;

                    if (!is_string($plain)) {
                        continue;
                    }

                    $normalized = trim($plain);

                    if ($normalized === '' || !filter_var($normalized, FILTER_VALIDATE_URL)) {
                        continue;
                    }

                    if (is_string($workshop->meeting_link_cipher) && $workshop->meeting_link_cipher !== '') {
                        continue;
                    }

                    try {
                        $encrypted = Crypt::encryptString($normalized);
                    } catch (\Throwable) {
                        continue;
                    }

                    $hashKey = config('app.key', 'wasfah-workshop');
                    $hashedIndicator = hash_hmac('sha256', $normalized, (string) $hashKey);

                    DB::table('workshops')
                        ->where('id', $workshop->id)
                        ->update([
                            'meeting_link' => $hashedIndicator,
                            'meeting_link_cipher' => $encrypted,
                        ]);
                }
            });
    }

    public function down(): void
    {
        DB::table('workshops')
            ->whereNotNull('meeting_link_cipher')
            ->orderBy('id')
            ->chunkById(100, function ($workshops) {
                foreach ($workshops as $workshop) {
                    $cipher = $workshop->meeting_link_cipher;

                    if (!is_string($cipher) || trim($cipher) === '') {
                        continue;
                    }

                    try {
                        $plain = Crypt::decryptString($cipher);
                    } catch (\Throwable) {
                        continue;
                    }

                    DB::table('workshops')
                        ->where('id', $workshop->id)
                        ->update([
                            'meeting_link' => $plain,
                            'meeting_link_cipher' => null,
                        ]);
                }
            });

        Schema::table('workshops', function (Blueprint $table) {
            if (Schema::hasColumn('workshops', 'meeting_link_cipher')) {
                $table->dropColumn('meeting_link_cipher');
            }
        });
    }
};
