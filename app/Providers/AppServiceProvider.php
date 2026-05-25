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

        if (! $this->documentRootRequiresPublicPrefix()) {
            return;
        }

        $publicUrl = rtrim(config('app.url'), '/').'/public';

        config([
            'app.asset_url' => $publicUrl,
            'filesystems.disks.public.url' => env('STORAGE_URL', $publicUrl.'/storage'),
        ]);
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
