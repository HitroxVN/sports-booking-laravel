<?php

namespace App\Providers;

use App\Models\Review;
use App\Observers\ReviewObserver;
use Illuminate\Foundation\DevCommands;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Review::observe(ReviewObserver::class);

        // Process đi kèm lệnh `php artisan dev` (dev-only): schedule + reverb
        // Dùng DevCommands::artisan() để bọc "php artisan ..." — register() chạy lệnh thô,
        // Windows không hiểu "schedule:work" như một chương trình
        if ($this->app->isLocal()) {
            DevCommands::artisan('schedule:work', 'schedule');
            DevCommands::artisan('reverb:start', 'reverb');
        }
    }
}
