<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class TestMobileViewport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'viewport:test 
                            {--strict : Fail with exit code 1 on any critical viewport or Web Vitals issue}
                            {--file= : Specific file or directory path to test}
                            {--format=table : Output format (table or json)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test and audit mobile viewport responsiveness and Core Web Vitals without using a browser';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Starting Browserless Mobile Viewport & Web Vitals Audit...');

        $scriptPath = base_path('.agents/skills/mobile-viewport-test/scripts/audit.mjs');

        if (!file_exists($scriptPath)) {
            $this->error("Audit runner script not found at: {$scriptPath}");
            return Command::FAILURE;
        }

        $command = ['node', $scriptPath];

        if ($this->option('strict')) {
            $command[] = '--strict';
        }

        if ($file = $this->option('file')) {
            $command[] = "--target={$file}";
        }

        if ($this->option('format') === 'json') {
            $command[] = '--format=json';
        }

        $process = new Process($command, base_path());
        $process->setTimeout(120);

        // Stream output in real-time
        $process->run(function ($type, $buffer) {
            $this->output->write($buffer);
        });

        return $process->isSuccessful() ? Command::SUCCESS : Command::FAILURE;
    }
}
