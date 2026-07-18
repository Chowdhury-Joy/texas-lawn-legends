<?php

namespace App\Models;

use Database\Factories\SettingFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    /** @use HasFactory<SettingFactory> */
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
    ];

    /**
     * Resolve a setting value by key, casting it according to its declared type.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        // Cache the resolved (casted) value rather than the Eloquent model —
        // caching a full model instance breaks on unserialize across requests.
        $value = Cache::rememberForever("setting.{$key}", function () use ($key) {
            $setting = static::query()->where('key', $key)->first();

            return $setting ? ['value' => $setting->castedValue()] : null;
        });

        return $value === null ? $default : $value['value'];
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

        Cache::forget("setting.{$key}");

        return $setting;
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
        static::saved(fn (Setting $setting) => Cache::forget("setting.{$setting->key}"));
        static::deleted(fn (Setting $setting) => Cache::forget("setting.{$setting->key}"));
    }
}
