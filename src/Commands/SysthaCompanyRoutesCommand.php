<?php

namespace Systha\CompanyPanel\Commands;

use Illuminate\Console\Command;

class SysthaCompanyRoutesCommand extends Command
{
    protected $signature = 'systha:company:routes';

    protected $description = 'Canonical command alias for CompanyPanel:routes.';

    public function handle(): int
    {
        return (int) $this->call('CompanyPanel:routes');
    }
}
