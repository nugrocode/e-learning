<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator; // <--- PENTING: Import Class Paginator

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
        // Mengubah default styling pagination Laravel ke Bootstrap 5
        // Agar tombol "Next/Previous" di tabel user tampil rapi.
        Paginator::useBootstrapFive();
    }
}
