<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SystemSetting extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
        'label',
        'description',
    ];

    /**
     * Get a setting value by key with cache and default fallback
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $value = Cache::remember("setting_{$key}", 3600, function () use ($key) {
            $setting = self::where('key', $key)->first();
            return $setting ? $setting->value : null;
        });

        if ($value === null) {
            return $default;
        }

        return $value;
    }

    /**
     * Get a float setting value (e.g. fee percent)
     */
    public static function getFloat(string $key, float $default = 0.0): float
    {
        return (float) self::get($key, $default);
    }

    /**
     * Set/Update a setting value
     */
    public static function set(string $key, mixed $value, ?string $group = null, ?string $label = null, ?string $description = null): self
    {
        Cache::forget("setting_{$key}");

        return self::updateOrCreate(
            ['key' => $key],
            array_filter([
                'value' => (string) $value,
                'group' => $group,
                'label' => $label,
                'description' => $description,
            ], fn($v) => $v !== null)
        );
    }
}
