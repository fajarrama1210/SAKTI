<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
        \Illuminate\Pagination\Paginator::useBootstrapFive();

        // Share unpaid bills count to student bottom nav
        View::composer('student.layouts.bottom-nav', function ($view) {
            $unpaidCount = 0;
            if (Auth::check() && Auth::user()->role === 'student' && Auth::user()->student_id) {
                $unpaidCount = DB::table('bills')
                    ->where('student_id', Auth::user()->student_id)
                    ->whereIn('status', ['unpaid', 'partial'])
                    ->count();
            }
            $view->with('unpaidBillsCount', $unpaidCount);
        });
    }
}
