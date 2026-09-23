<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;

/**
 * Cron-less scheduler: chạy `schedule:run` trong request khi không có cron hệ thống
 * (XAMPP Windows). Cache lock đảm bảo tối đa 1 lần/phút dù có bao nhiêu request.
 * Kiểu wp-cron của WordPress — chỉ chạy khi có người truy cập site.
 */
class RunScheduler
{
    public function handle(Request $request, Closure $next)
    {
        // Cache lock 55s — chỉ request đầu tiên trong mỗi phút kích hoạt schedule:run
        if (Cache::lock('schedule-run', 55)->get()) {
            Artisan::call('schedule:run');
        }

        return $next($request);
    }
}
