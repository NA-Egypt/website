<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CommitteeReport;
use App\Services\ReportArchiver;

class ArchiveReports extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reports:archive-sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync all existing approved committee reports and their attachments to the Storage Box archive';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(ReportArchiver $archiver)
    {
        $this->info('Starting sync of approved reports to the Storage Box archive...');

        $reports = CommitteeReport::where('status', 'approved')
            ->with('attachments')
            ->get();

        if ($reports->isEmpty()) {
            $this->info('No approved reports found to archive.');
            return Command::SUCCESS;
        }

        $this->info(sprintf('Found %d approved reports. Archiving...', $reports->count()));

        $bar = $this->output->createProgressBar($reports->count());
        $bar->start();

        $successCount = 0;
        $failCount = 0;

        foreach ($reports as $report) {
            $success = $archiver->archive($report);
            if ($success) {
                $successCount++;
            } else {
                $failCount++;
            }
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();

        $this->info("Archive sync completed.");
        $this->info("Successfully archived: {$successCount} reports.");
        if ($failCount > 0) {
            $this->error("Failed to archive: {$failCount} reports. Check logs for details.");
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
