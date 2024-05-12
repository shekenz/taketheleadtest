<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBeUnique;
use App\Services\ComicAPIService;
use App\Models\Comic;
use Carbon\Carbon;
use Illuminate\Bus\Batchable;
use Illuminate\Support\Facades\Redis;

final class ProcessComicChunk implements ShouldQueue, ShouldBeUnique
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * Create a new job instance.
     */
    public function __construct(public int $chunkIndex, public bool $lastChunk = false)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if ((bool) Redis::get('bypassJob')) {
            return;
        }

        $chunkData = ComicAPIService::fetchChunk($this->chunkIndex);

        $comicsToUpsert = [];
        foreach ($chunkData->data->results as $comic) {

            $detailsUrl = null;
            foreach ($comic->urls as $url) {
                if ($url->type === 'detail') {
                    $detailsUrl = $url->url;
                    break;
                }
            }

            $releaseDate = null;
            foreach ($comic->dates as $date) {
                if ($date->type === 'onsaleDate') {
                    $releaseDate = Carbon::parse($date->date)->toDateTimeString();
                    break;
                }
            }
            if ($releaseDate === '-0001-11-30 00:00:00') {
                $releaseDate = null;
            }

            $comicsToUpsert[] = [
                'marvel_id' => $comic->id,
                'title' => $comic->title,
                'details_url' => $detailsUrl,
                'thumbnail' => $comic->thumbnail->path,
                'released_on' => $releaseDate,
            ];
        }

        // Somehow no exception is raised when job fails because of SQL error.
        try {
            Comic::upsert($comicsToUpsert, uniqueBy: ['marvel_id']);
        } catch (\Throwable $e) {
            echo $e->getMessage() . "\n";
        }
    }
}
