<?php

/**
 * Vite Fallback Helper
 * Provides fallback for Vite assets when manifest.json is not available
 */

if (!function_exists('vite_fallback')) {
    function vite_fallback($assets = [], $fallback = true) {
        if ($fallback && !file_exists(public_path('build/manifest.json'))) {
            // Return fallback assets when Vite manifest is not available
            $html = '';
            
            foreach ($assets as $asset) {
                if (str_ends_with($asset, '.css')) {
                    $html .= '<link rel="stylesheet" href="' . asset('css/' . basename($asset)) . '">' . "\n";
                } elseif (str_ends_with($asset, '.js')) {
                    $html .= '<script src="' . asset('js/' . basename($asset)) . '"></script>' . "\n";
                }
            }
            
            return $html;
        }
        
        return '';
    }
}

if (!function_exists('vite_assets')) {
    function vite_assets($assets = []) {
        try {
            return app('vite')->__invoke($assets);
        } catch (\Illuminate\Foundation\ViteManifestNotFoundException $e) {
            // Fallback to static assets
            return vite_fallback($assets);
        }
    }
}
