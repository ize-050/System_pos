<?php

namespace App\Helpers;

class ViteHelper
{
    public static function asset($entry)
    {
        $isDev = app()->environment('local');
        
        if ($isDev) {
            // Development mode - use Vite dev server
            $devServerUrl = 'http://localhost:5173';
            
            // Check if Vite dev server is running
            $hotFile = public_path('hot');
            if (file_exists($hotFile)) {
                $devServerUrl = trim(file_get_contents($hotFile));
            }
            
            if (str_ends_with($entry, '.js')) {
                return '<script type="module" src="' . $devServerUrl . '/' . $entry . '"></script>';
            } elseif (str_ends_with($entry, '.css')) {
                return '<link rel="stylesheet" href="' . $devServerUrl . '/' . $entry . '">';
            }
        } else {
            // Production mode - use built assets
            $manifestPath = public_path('build/manifest.json');
            
            if (file_exists($manifestPath)) {
                $manifest = json_decode(file_get_contents($manifestPath), true);
                
                if (isset($manifest[$entry])) {
                    $file = $manifest[$entry]['file'];
                    
                    if (str_ends_with($entry, '.js')) {
                        return '<script type="module" src="/build/' . $file . '"></script>';
                    } elseif (str_ends_with($entry, '.css')) {
                        return '<link rel="stylesheet" href="/build/' . $file . '">';
                    }
                }
            }
        }
        
        return '';
    }
    
    public static function reactRefresh()
    {
        if (app()->environment('local')) {
            $devServerUrl = 'http://localhost:5173';
            
            $hotFile = public_path('hot');
            if (file_exists($hotFile)) {
                $devServerUrl = trim(file_get_contents($hotFile));
            }
            
            return '<script type="module" src="' . $devServerUrl . '/@vite/client"></script>';
        }
        
        return '';
    }
}