<?php

namespace App\Console\Commands;

use App\Models\City;
use App\Models\DirectOnlineGroup;
use App\Models\Group;
use App\Models\Neighborhood;
use Illuminate\Console\Command;

class ReindexSearchText extends Command
{
    protected $signature = 'search:reindex';

    protected $description = 'Backfill normalized search_text column for groups, direct online groups, cities, and neighborhoods';

    public function handle(): int
    {
        $models = [
            'groups' => Group::class,
            'direct_online_groups' => DirectOnlineGroup::class,
            'cities' => City::class,
            'neighborhoods' => Neighborhood::class,
        ];

        foreach ($models as $label => $modelClass) {
            $count = 0;
            $modelClass::query()->chunkById(200, function ($records) use (&$count) {
                foreach ($records as $record) {
                    $record->refreshSearchText();
                    $record->saveQuietly();
                    $count++;
                }
            });
            $this->info("Reindexed {$count} {$label}.");
        }

        return self::SUCCESS;
    }
}
