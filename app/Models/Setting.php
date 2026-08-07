<?php

namespace App\Models;

use App\Models\Traits\BelongsToTrialWorkspace;
use App\Support\Trial\TrialWorkspaceContext;
use Database\Factories\SettingFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    /** @use HasFactory<SettingFactory> */
    use HasFactory;

    use BelongsToTrialWorkspace;

    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
        'trial_workspace_id',
    ];

    /**
     * Per-request memo of resolved payloads, keyed by cache key.
     *
     * @var array<string, array{hit: bool, value: mixed}>
     */
    private static array $requestCache = [];

    public static function get(string $key, mixed $default = null): mixed
    {
        $payload = static::$requestCache[static::cacheKey($key)] ??= static::resolvePayload($key);

        return $payload['hit'] ? $payload['value'] : $default;
    }

    /**
     * @return array{hit: bool, value: mixed}
     */
    private static function resolvePayload(string $key): array
    {
        $cacheKey = static::cacheKey($key);

        $payload = Cache::rememberForever($cacheKey, function () use ($key) {
            $query = static::query()->where('key', $key);
            $setting = $query->first();

            return $setting
                ? ['hit' => true, 'value' => $setting->castedValue()]
                : ['hit' => false, 'value' => null];
        });

        if (! is_array($payload) || ! array_key_exists('hit', $payload)) {
            $payload = is_array($payload) && array_key_exists('value', $payload)
                ? ['hit' => true, 'value' => $payload['value']]
                : ['hit' => false, 'value' => null];

            Cache::forever($cacheKey, $payload);
        }

        return $payload;
    }

    public static function set(string $key, mixed $value, string $type = 'string', string $group = 'general'): self
    {
        $encoded = in_array($type, ['json', 'array'], true) ? json_encode($value) : (string) $value;

        $attributes = ['key' => $key];
        $workspaceId = TrialWorkspaceContext::id();

        if ($workspaceId !== null) {
            $attributes['trial_workspace_id'] = $workspaceId;
        }

        $setting = static::query()->updateOrCreate(
            $attributes,
            ['value' => $encoded, 'type' => $type, 'group' => $group],
        );

        static::forget($key);

        return $setting;
    }

    public static function forget(string $key): void
    {
        unset(static::$requestCache[static::cacheKey($key)]);

        Cache::forget(static::cacheKey($key));
    }

    public static function flushRequestCache(): void
    {
        static::$requestCache = [];
    }

    private static function cacheKey(string $key): string
    {
        $workspaceId = TrialWorkspaceContext::id();

        return $workspaceId !== null
            ? "setting.ws{$workspaceId}.{$key}"
            : "setting.global.{$key}";
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
