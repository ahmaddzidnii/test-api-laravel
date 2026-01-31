<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class DeployApp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:deploy';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deploy the application by running necessary setup commands.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->callSilent('key:generate', ['--force' => true]);
        $this->call('migrate', ['--force' => true]);
        $this->callSilent('storage:link');
        $this->call('config:clear');
        $this->call('config:cache');
        $this->call('route:cache');
        $this->call('view:cache');
        $this->callSilent('queue:restart');
    }
}
