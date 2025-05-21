<?php

namespace Amplify\System\Sayt\Commands;

use Amplify\System\Sayt\Jobs\ReConfigSaytStore;
use Illuminate\Console\Command;


class ReconfigureSaytSearchCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sayt:reconfigure';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'SAYT Search configuration update';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        ReConfigSaytStore::dispatch();

        return self::SUCCESS;
    }
}
