<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DefaultConfig extends Model
{
    use HasFactory;
    protected $fillable = [
        'key',
        'value',
        'content',
    ];
    protected $casts = [
        'content' => 'json',
    ];


    public static function byKey($key)
    {
        return self::where('key', $key)->first();
    }

    public static function set($key, $value, array $content = array())
    {
        $config = self::firstOrNew(array('key' => $key));
        $config->key = $key;
        $config->value = $value;
        if (!empty($content)) {
            $config->content = $content;
        }
        $config->save();
        return $config;
    }
}
