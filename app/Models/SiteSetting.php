<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'type',
    ];

    /**
     * Get setting value by key
     */
    public static function get($key, $default = null)
    {
        $setting = self::where('key', $key)->first();
        if (!$setting) {
            return $default;
        }

        return match($setting->type) {
            'json' => json_decode($setting->value, true),
            'boolean' => (bool) $setting->value,
            default => $setting->value,
        };
    }

    /**
     * Set setting value by key
     */
    public static function set($key, $value, $type = 'text')
    {
        $setting = self::firstOrNew(['key' => $key]);
        
        if ($type === 'json') {
            $value = json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        } elseif ($type === 'boolean') {
            $value = $value ? '1' : '0';
        } else {
            $value = (string) $value;
        }
        
        $setting->value = $value;
        $setting->type = $type;
        $setting->save();
        
        return $setting;
    }
}
