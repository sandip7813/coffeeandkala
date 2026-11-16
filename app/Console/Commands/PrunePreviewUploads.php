<?php

namespace App\Console\Commands;

use App\Support\Preview\PreviewUploads;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

/**
 * Deletes the temporary images stored for admin previews (see
 * PreviewUploads) once they're old enough that no open preview tab still
 * needs them. Scheduled hourly in routes/console.php.
 */
class PrunePreviewUploads extends Command
{
    protected $signature = 'previews:prune {--hours=6 : Delete preview images older than this many hours}';

    protected $description = 'Delete temporary images uploaded for admin previews';

    public function handle(): int
    {
        $cutoff = now()->subHours(max(0, (int) $this->option('hours')))->getTimestamp();
        $deleted = 0;

        foreach (PreviewUploads::disks() as $disk) {
            $storage = Storage::disk($disk);

            foreach ($storage->files(PreviewUploads::DIRECTORY) as $path) {
                if ($storage->lastModified($path) <= $cutoff) {
                    $storage->delete($path);
                    $deleted++;
                }
            }
        }

        $this->info("Deleted {$deleted} preview image(s).");

        return self::SUCCESS;
    }
}
