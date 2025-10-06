<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use App\Helpers\ViteHelper;

class ViteServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // Register @vite blade directive
        Blade::directive('vite', function ($expression) {
            $assets = eval("return $expression;");
            $output = '';
            
            // Add Vite client for development
            $output .= ViteHelper::reactRefresh();
            
            // Add each asset
            foreach ($assets as $asset) {
                $output .= ViteHelper::asset($asset);
            }
            
            return $output;
        });
    }
}