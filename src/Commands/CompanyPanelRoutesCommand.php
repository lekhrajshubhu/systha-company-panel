<?php

namespace Systha\CompanyPanel\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;

class CompanyPanelRoutesCommand extends Command
{
    protected $signature = 'CompanyPanel:routes';

    protected $description = 'List CompanyPanel API routes only (api/company*).';

    public function handle(): int
    {
        $rows = $this->collectRows('api/company');

        if ($rows->isEmpty()) {
            $this->warn('No CompanyPanel API routes found for prefix: api/company');
            return self::SUCCESS;
        }

        $this->table(['METHOD', 'URI', 'NAME', 'ACTION', 'MIDDLEWARE'], $rows->all());

        return self::SUCCESS;
    }

    /**
     * @return Collection<int, array<int, string>>
     */
    private function collectRows(string $prefix): Collection
    {
        return collect(Route::getRoutes()->getRoutes())
            ->filter(function ($route) use ($prefix): bool {
                return str_starts_with($route->uri(), $prefix);
            })
            ->map(function ($route): array {
                $methods = implode('|', array_values(array_diff($route->methods(), ['HEAD'])));
                $middleware = implode(', ', $route->middleware());

                return [
                    $methods,
                    $route->uri(),
                    (string) ($route->getName() ?? ''),
                    $route->getActionName(),
                    $middleware,
                ];
            })
            ->sortBy(fn (array $row): string => $row[1] . '|' . $row[0])
            ->values();
    }
}
