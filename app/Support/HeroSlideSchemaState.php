<?php

namespace App\Support;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Throwable;

class HeroSlideSchemaState
{
    private const TABLE = 'hero_slides';

    /**
     * Columns the feature cannot operate without (apart from media columns).
     *
     * @var array<int, string>
     */
    private const BASE_COLUMNS = [
        'id',
        'badge',
        'title',
        'description',
        'image_alt',
        'features',
        'actions',
        'is_active',
        'sort_order',
        'created_at',
        'updated_at',
    ];

    private const LEGACY_MEDIA_COLUMN = 'image_path';

    /**
     * @var array<int, string>
     */
    private const MODERN_MEDIA_COLUMNS = ['desktop_image_path', 'mobile_image_path'];

    /**
     * Whether the hero_slides table is present and contains the columns we rely on.
     */
    public static function isReady(): bool
    {
        $columns = self::columns();

        if (empty($columns)) {
            return false;
        }

        if (!empty(array_diff(self::BASE_COLUMNS, $columns))) {
            return false;
        }

        return self::hasModernMediaColumns($columns) || self::hasLegacyMediaColumn($columns);
    }

    /**
     * Returns insight about the schema health that can be rendered in the UI.
     *
     * @return array{ready: bool, missing_columns: array<int, string>, media_mode: string}
     */
    public static function describe(): array
    {
        $columns = self::columns();
        $missingColumns = self::missingColumnsFrom($columns);

        return [
            'ready' => empty($missingColumns),
            'missing_columns' => $missingColumns,
            'media_mode' => self::mediaMode($columns),
        ];
    }

    /**
     * Identify which columns are currently missing.
     *
     * @return array<int, string>
     */
    public static function missingColumns(): array
    {
        return self::missingColumnsFrom(self::columns());
    }

    /**
     * Whether the table falls back to the legacy single image column.
     */
    public static function usesLegacyMediaColumn(): bool
    {
        $columns = self::columns();

        return !self::hasModernMediaColumns($columns) && self::hasLegacyMediaColumn($columns);
    }

    /**
     * @return array<int, string>
     */
    protected static function missingColumnsFrom(array $columns): array
    {
        if (empty($columns)) {
            return array_merge(self::BASE_COLUMNS, self::MODERN_MEDIA_COLUMNS);
        }

        $missing = array_values(array_diff(self::BASE_COLUMNS, $columns));

        if (!self::hasModernMediaColumns($columns) && !self::hasLegacyMediaColumn($columns)) {
            $missing = array_merge($missing, self::MODERN_MEDIA_COLUMNS);
        }

        return array_values(array_unique($missing));
    }

    /**
     * @return array<int, string>
     */
    protected static function columns(): array
    {
        try {
            if (!Schema::hasTable(self::TABLE)) {
                return [];
            }

            return Schema::getColumnListing(self::TABLE);
        } catch (Throwable $exception) {
            Log::warning('Unable to inspect the hero_slides schema; assuming it is not ready.', [
                'message' => $exception->getMessage(),
                'exception' => $exception::class,
            ]);

            return [];
        }
    }

    /**
     * @param array<int, string> $columns
     */
    protected static function hasModernMediaColumns(array $columns): bool
    {
        return empty(array_diff(self::MODERN_MEDIA_COLUMNS, $columns));
    }

    /**
     * @param array<int, string> $columns
     */
    protected static function hasLegacyMediaColumn(array $columns): bool
    {
        return in_array(self::LEGACY_MEDIA_COLUMN, $columns, true);
    }

    /**
     * @param array<int, string> $columns
     */
    protected static function mediaMode(array $columns): string
    {
        if (self::hasModernMediaColumns($columns)) {
            return 'dual';
        }

        if (self::hasLegacyMediaColumn($columns)) {
            return 'legacy';
        }

        return 'missing';
    }
}
