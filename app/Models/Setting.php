<?php

namespace App\Models;

use App\Models\Traits\TriggersSiteReload;
use Database\Factories\SettingFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    /** @use HasFactory<SettingFactory> */
    use HasFactory;

    use TriggersSiteReload;

    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
    ];

    /**
     * Per-request memo of resolved payloads, keyed by setting key.
     *
     * A single page render reads dozens of keys, often repeatedly. Without this
     * every read is a cache-store round trip (the default store is the database).
     *
     * @var array<string, array{hit: bool, value: mixed}>
     */
    private static array $requestCache = [];

    /**
     * Resolve a setting value by key, casting it according to its declared type.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $payload = static::$requestCache[$key] ??= static::resolvePayload($key);

        return $payload['hit'] ? $payload['value'] : $default;
    }

    /**
     * Load a key's payload from the persistent cache, falling back to the DB.
     *
     * The payload records whether the row exists so a legitimately null value is
     * distinguishable from a missing key — otherwise misses are never cached and
     * re-query on every call.
     *
     * @return array{hit: bool, value: mixed}
     */
    private static function resolvePayload(string $key): array
    {
        // Cache the resolved (casted) value rather than the Eloquent model —
        // caching a full model instance breaks on unserialize across requests.
        $payload = Cache::rememberForever("setting.{$key}", function () use ($key) {
            $setting = static::query()->where('key', $key)->first();

            return $setting
                ? ['hit' => true, 'value' => $setting->castedValue()]
                : ['hit' => false, 'value' => null];
        });

        // Payloads written by an older release stored either null or ['value' => x].
        if (! is_array($payload) || ! array_key_exists('hit', $payload)) {
            $payload = is_array($payload) && array_key_exists('value', $payload)
                ? ['hit' => true, 'value' => $payload['value']]
                : ['hit' => false, 'value' => null];

            Cache::forever("setting.{$key}", $payload);
        }

        return $payload;
    }

    /**
     * Persist a setting value, encoding arrays as JSON, and bust the cache.
     */
    public static function set(string $key, mixed $value, string $type = 'string', string $group = 'general'): self
    {
        $encoded = in_array($type, ['json', 'array'], true) ? json_encode($value) : (string) $value;

        $setting = static::query()->updateOrCreate(
            ['key' => $key],
            ['value' => $encoded, 'type' => $type, 'group' => $group],
        );

        static::forget($key);

        return $setting;
    }

    /**
     * Drop a key from both the persistent cache and the per-request memo.
     */
    public static function forget(string $key): void
    {
        unset(static::$requestCache[$key]);

        Cache::forget("setting.{$key}");
    }

    /**
     * Clear the per-request memo (tests, queue workers, long-running processes).
     */
    public static function flushRequestCache(): void
    {
        static::$requestCache = [];
    }

    public function castedValue(): mixed
    {
        return match ($this->type) {
            'json', 'array' => json_decode((string) $this->value, true),
            'boolean', 'bool' => filter_var($this->value, FILTER_VALIDATE_BOOLEAN),
            'integer', 'int' => (int) $this->value,
            'decimal', 'float' => (float) $this->value,
            default => $this->value,
        };
    }

    protected static function booted(): void
    {
        static::saved(fn (Setting $setting) => static::forget($setting->key));
        static::deleted(fn (Setting $setting) => static::forget($setting->key));
    }
}
