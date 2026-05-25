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
        $this->configureUrlsForRootDocumentRoot();
    }

    /**
     * When the web server document root is the project root (not public/),
     * static files and storage live under public/ but URLs omit that prefix.
     * Apache .htaccess rewrites fix this locally; nginx ignores .htaccess.
     */
    protected function configureUrlsForRootDocumentRoot(): void
    {
        if (PHP_SAPI === 'cli' || empty($_SERVER['DOCUMENT_ROOT'])) {
            return;
        }

        if (! $this->documentRootRequiresPublicPrefix()) {
            return;
        }

        $publicUrl = rtrim(config('app.url'), '/').'/public';
        $storageUrl = env('STORAGE_URL', $publicUrl.'/storage');

        if (! config('app.asset_url')) {
            config(['app.asset_url' => $publicUrl]);
        }

        config(['filesystems.disks.public.url' => $storageUrl]);
    }

    protected function documentRootRequiresPublicPrefix(): bool
    {
        $documentRoot = realpath($_SERVER['DOCUMENT_ROOT']);
        $basePath = realpath(base_path());
        $publicPath = realpath(public_path());

        if (! $publicPath || ! is_dir($publicPath.'/themes')) {
            return false;
        }

        if ($documentRoot && $basePath && $documentRoot === $basePath) {
            return true;
        }

        if ($documentRoot && ! is_file($documentRoot.'/themes/shopwise/css/style.css')) {
            return is_file($publicPath.'/themes/shopwise/css/style.css');
        }

        return false;
    }
}
