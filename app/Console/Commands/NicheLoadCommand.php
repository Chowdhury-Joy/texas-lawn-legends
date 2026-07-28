<?php

namespace App\Console\Commands;

use App\Support\Niche\NicheLoader;
use App\Support\Niche\NicheResolver;
use Illuminate\Console\Command;

class NicheLoadCommand extends Command
{
    protected $signature = 'niche:load
                            {niche? : Pack id (lawn, cleaning, roofing). Defaults to current active pack.}
                            {--reset : Reload the current active pack}
                            {--no-demo : Load without enabling demo_mode banner}';

    protected $description = 'Load (or reset) an industry niche pack and its starter content';

    public function handle(NicheLoader $loader): int
    {
        $niche = $this->option('reset')
            ? NicheResolver::activeId()
            : (string) ($this->argument('niche') ?: NicheResolver::activeId());

        $demoMode = ! $this->option('no-demo');

        $this->info("Loading niche pack [{$niche}]".($demoMode ? ' (demo mode)' : '').'…');

        try {
            $pack = $loader->load($niche, $demoMode);
        } catch (\Throwable $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }

        $this->info("Active pack: {$pack->label()} ({$pack->id()})");

        return self::SUCCESS;
    }
}
