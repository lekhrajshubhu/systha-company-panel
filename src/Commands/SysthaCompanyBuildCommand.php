<?php

namespace Systha\CompanyPanel\Commands;

use Illuminate\Console\Command;

class SysthaCompanyBuildCommand extends Command
{
    protected $signature = 'systha:company:build';

    protected $description = 'Canonical command alias for CompanyPanel:setup --skip-install.';

    public function handle(): int
    {
        return (int) $this->call('CompanyPanel:setup', ['--skip-install' => true]);
    }
}
