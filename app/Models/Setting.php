<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class Setting extends Model
{
    protected $primaryKey = 'key';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['key', 'value'];

    /**
     * Lấy giá trị setting theo key (có cache). Trả về default nếu chưa có.
     */
    public static function get(string $key, ?string $default = null): ?string
    {
        $all = Cache::rememberForever('settings.all', fn () => self::pluck('value', 'key')->all());

        return $all[$key] ?? $default;
    }

    /**
     * Ghi setting và làm mới cache.
     */
    public static function set(string $key, ?string $value): void
    {
        self::updateOrCreate(['key' => $key], ['value' => $value]);
        Cache::forget('settings.all');
    }

    /**
     * Giá trị setting dạng URL công khai: nếu là đường dẫn file (logo upload) → asset('storage/...'),
     * nếu chưa có → fallback về mặc định truyền vào.
     */
    public static function asset(string $key, string $default): string
    {
        $value = self::get($key);

        return $value ? asset('storage/' . $value) : $default;
    }

    /**
     * Xóa file setting cũ khỏi disk (dùng khi thay logo).
     */
    public static function deleteFile(?string $path): void
    {
        if ($path && ! str_starts_with($path, 'images/')) {
            Storage::disk('public')->delete($path);
        }
    }
}
