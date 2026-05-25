<?php

namespace App\Providers;

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
        $this->configureAssetUrlsForRootDocumentRoot();
    }

    /**
     * When the web server document root is the project root (not public/),
     * static files live under public/ but asset URLs omit that prefix.
     * Apache .htaccess rewrites fix this locally; nginx ignores .htaccess,
     * so we prefix asset and storage URLs with /public automatically.
     */
    protected function configureAssetUrlsForRootDocumentRoot(): void
    {
        if (config('app.asset_url')) {
            return;
        }

        if (PHP_SAPI === 'cli' || empty($_SERVER['DOCUMENT_ROOT'])) {
            return;
        }

        $documentRoot = realpath($_SERVER['DOCUMENT_ROOT']);
        $basePath = realpath(base_path());

        if (! $documentRoot || ! $basePath || $documentRoot !== $basePath) {
            return;
        }

        $publicUrl = rtrim(config('app.url'), '/').'/public';

        config([
            'app.asset_url' => $publicUrl,
            'filesystems.disks.public.url' => $publicUrl.'/storage',
        ]);
    }
}
