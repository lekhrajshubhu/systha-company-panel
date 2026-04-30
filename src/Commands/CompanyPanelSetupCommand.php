<?php

namespace Systha\CompanyPanel\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Symfony\Component\Process\Process;

class CompanyPanelSetupCommand extends Command
{
    protected $signature = 'companypanel:setup {--skip-install : Skip npm install/ci and only run build}';

    protected $description = 'Build company panel assets and publish to public/company';

    public function handle(): int
    {
        $packageRoot = dirname(__DIR__, 2);
        $frontendPath = $packageRoot . '/' . trim((string) config('CompanyPanel.frontend_path', 'resources'), '/');
        $distPath = $frontendPath . '/dist';
        $targetPath = public_path(config('CompanyPanel.public_path', 'company'));

        if (!is_dir($frontendPath)) {
            $this->error("Frontend path not found: {$frontendPath}");
            return self::FAILURE;
        }

        if (!(bool) $this->option('skip-install')) {
            $installCmd = file_exists($packageRoot . '/package-lock.json') ? ['npm', 'ci'] : ['npm', 'install'];
            if (!$this->runProcess($installCmd, $packageRoot, 'Installing npm dependencies')) {
                return self::FAILURE;
            }
        }

        if (!$this->runProcess(['npm', 'run', 'build'], $packageRoot, 'Building company panel assets')) {
            return self::FAILURE;
        }

        if (!is_dir($distPath)) {
            $this->error("Build output not found at: {$distPath}");
            return self::FAILURE;
        }

        if (is_dir($targetPath)) {
            File::deleteDirectory($targetPath);
        }

        File::makeDirectory($targetPath, 0755, true);
        File::copyDirectory($distPath, $targetPath);

        $this->info("Company panel assets published to: {$targetPath}");

        return self::SUCCESS;
    }

    private function runProcess(array $command, string $cwd, string $title): bool
    {
        $this->info($title . '...');

        $process = new Process($command, $cwd);
        $process->setTimeout(1800);
        $process->run(function ($type, $buffer) {
            $this->output->write($buffer);
        });

        if (!$process->isSuccessful()) {
            $this->error('Failed: ' . implode(' ', $command));
            return false;
        }

        return true;
    }
}
