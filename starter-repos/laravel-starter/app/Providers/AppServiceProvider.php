<?php

namespace App\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Share the authenticated user's unread notification count with the
        // layout so the nav badge is correct on every page.
        View::composer('layouts.app', function ($view) {
            $count = Auth::check()
                ? Auth::user()->notifications()->whereNull('read_at')->count()
                : 0;

            $view->with('unreadCount', $count);
        });
    }
}
