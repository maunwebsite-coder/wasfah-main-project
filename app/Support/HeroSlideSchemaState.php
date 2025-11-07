<?php

namespace App\Support;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Throwable;

class HeroSlideSchemaState
{
    private const TABLE = 'hero_slides';

    /**
     * Columns the hero slides feature expects to exist.
     *
     * @var array<int, string>
     */
    private const REQUIRED_COLUMNS = [
        'id',
        'badge',
        'title',
        'description',
        'image_alt',
        'desktop_image_path',
        'mobile_image_path',
        'features',
        'actions',
        'is_active',
        'sort_order',
        'created_at',
        'updated_at',
    ];

    /**
     * Whether the hero_slides table is present and contains the columns we rely on.
     */
    public static function isReady(): bool
    {
        return empty(self::missingColumns());
    }

    /**
     * Returns insight about the schema health that can be rendered in the UI.
     *
     * @return array{ready: bool, missing_columns: array<int, string>}
     */
    public static function describe(): array
    {
        $missingColumns = self::missingColumns();

        return [
            'ready' => empty($missingColumns),
            'missing_columns' => $missingColumns,
        ];
    }

    /**
     * Identify which columns are currently missing.
     *
     * @return array<int, string>
     */
    public static function missingColumns(): array
    {
        try {
            if (!Schema::hasTable(self::TABLE)) {
                return self::REQUIRED_COLUMNS;
            }

            $columns = Schema::getColumnListing(self::TABLE);
        } catch (Throwable $exception) {
            Log::warning('Unable to inspect the hero_slides schema; assuming it is not ready.', [
                'message' => $exception->getMessage(),
                'exception' => $exception::class,
            ]);

            return self::REQUIRED_COLUMNS;
        }

        return array_values(array_diff(self::REQUIRED_COLUMNS, $columns));
    }
}
