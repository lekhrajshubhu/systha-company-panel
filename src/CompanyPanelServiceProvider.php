<?php

namespace Systha\CompanyPanel;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class CompanyPanelServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/CompanyPanel.php', 'CompanyPanel');

    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../config/CompanyPanel.php' => config_path('CompanyPanel.php'),
        ], 'CompanyPanel-config');

        $this->loadRoutesFrom(__DIR__ . '/../routes/api.php');
        $this->registerCompanySpaFallbackRoute();
        $this->registerLegacyCompanySpaRedirect();

        if ($this->app->runningInConsole()) {
            $this->commands($this->discoverCommands());
        }
    }

    private function discoverCommands(): array
    {
        $commands = [];
        $namespace = __NAMESPACE__ . '\\Commands\\';

        foreach (glob(__DIR__ . '/Commands/*Command.php') as $file) {
            $class = $namespace . pathinfo($file, PATHINFO_FILENAME);

            if (class_exists($class)) {
                $commands[] = $class;
            }
        }

        return $commands;
    }

    private function registerCompanySpaFallbackRoute(): void
    {
        $prefix = trim((string) config('CompanyPanel.public_path', 'company'), '/');
        $indexPath = public_path($prefix . '/index.html');

        Route::middleware('web')
            ->get($prefix . '/{any?}', function (Request $request) use ($indexPath, $prefix) {

                $relativePath = ltrim(str_replace($prefix, '', $request->path()), '/');
                $requestedFile = public_path($prefix . ($relativePath !== '' ? '/' . $relativePath : ''));

                if ($relativePath !== '' && is_file($requestedFile)) {
                    return response()->file($requestedFile);
                }

                if (!is_file($indexPath)) {
                    abort(404, 'Company panel build not found. Run: php artisan companypanel:setup');
                }

                return response()->file($indexPath);
            })
            ->where('any', '.*');
    }

    private function registerLegacyCompanySpaRedirect(): void
    {
        $prefix = trim((string) config('CompanyPanel.public_path', 'company'), '/');
        $legacyPrefixes = [
            'companys',
            'company-panel',
        ];

        foreach ($legacyPrefixes as $legacyPrefix) {
            if ($prefix === $legacyPrefix) {
                continue;
            }

            Route::middleware('web')
                ->get($legacyPrefix . '/{any?}', function (Request $request) use ($prefix, $legacyPrefix) {
                    $relativePath = ltrim(str_replace($legacyPrefix, '', $request->path()), '/');
                    $target = '/' . $prefix . ($relativePath !== '' ? '/' . $relativePath : '');

                    return redirect($target);
                })
                ->where('any', '.*');
        }
    }
}
