<?php

declare(strict_types=1);

/**
 * Polyfills for Laravel Scout so the application keeps working even when the
 * package is missing on the target server. We only define these classes/traits
 * when the real implementations are unavailable.
 */
namespace Laravel\Scout;

if (! trait_exists(Searchable::class)) {
    trait Searchable
    {
        public static function bootSearchable(): void
        {
            // Scout is missing, so we cannot register model observers.
        }

        public static function makeAllSearchableUsing(?callable $callback = null): void
        {
            // No-op when Scout is absent.
        }

        public static function makeAllSearchable($chunk = null): void
        {
            // No-op when Scout is absent.
        }

        public static function removeAllFromSearch(): void
        {
            // No-op when Scout is absent.
        }

        public function searchable(): static
        {
            return $this;
        }

        public function unsearchable(): static
        {
            return $this;
        }

        public function queueMakeSearchable($models): void
        {
            // No-op when Scout is absent.
        }

        public function queueRemoveFromSearch($models): void
        {
            // No-op when Scout is absent.
        }

        public function scoutMetadata(): array
        {
            return [];
        }

        public function searchableAs(): string
        {
            return method_exists($this, 'getTable') ? (string) $this->getTable() : 'default';
        }

        public function searchableUsing()
        {
            throw new \RuntimeException('Laravel Scout is not installed.');
        }

        public function shouldBeSearchable(): bool
        {
            return true;
        }

        public function toSearchableArray(): array
        {
            return method_exists($this, 'toArray') ? (array) $this->toArray() : [];
        }
    }
}

namespace Laravel\Scout\Attributes;

if (! class_exists(SearchUsingFullText::class)) {
    #[\Attribute(\Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
    class SearchUsingFullText
    {
        public function __construct(public array $columns)
        {
        }
    }
}

if (! class_exists(SearchUsingPrefix::class)) {
    #[\Attribute(\Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
    class SearchUsingPrefix
    {
        public function __construct(public array $columns)
        {
        }
    }
}
