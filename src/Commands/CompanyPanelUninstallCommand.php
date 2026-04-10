<?php

namespace Systha\CompanyPanel\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class CompanyPanelUninstallCommand extends Command
{
    protected $signature = 'CompanyPanel:uninstall';

    protected $description = 'Remove published company panel assets from public/company-panel';

    public function handle(): int
    {
        $targetPath = public_path(config('CompanyPanel.public_path', 'company-panel'));

        if (!is_dir($targetPath)) {
            $this->warn("Nothing to remove. Path not found: {$targetPath}");
            return self::SUCCESS;
        }

        File::deleteDirectory($targetPath);

        $this->info("Removed company panel assets: {$targetPath}");

        return self::SUCCESS;
    }
}
