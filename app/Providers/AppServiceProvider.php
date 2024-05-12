<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Queue;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Queue\Events\JobProcessing;
use App\Models\ImportedComicChunk;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Queue::before(function (JobProcessing $event) {

            $jobPayload = $event->job->payload();
            $jobData = unserialize($jobPayload['data']['command']);

            Log::channel('marvelapi')->info("Handling chunk {$jobData->chunkIndex}");

            $chunk = ImportedComicChunk::updateOrCreate([
                'chunk_number' => $jobData->chunkIndex,
            ],
            [
                'last_job_uuid' => $event->job->uuid(),
                'started_at' => Carbon::now()->toDateTimeString(),
                'ended_at' => null,
                'bypassed' => false,
            ]);

            if ($chunk->status === 'processed' && !$jobData->lastChunk) {
                Log::channel('marvelapi')->info("Chunk already processed, passing by");
                $chunk->ended_at = Carbon::now()->toDateTimeString();
                $chunk->save();
                Redis::set('bypassJob', true);
            } else {
                Redis::set('bypassJob', false);
            }
        });

        Queue::after(function (JobProcessed $event) {

            $jobPayload = $event->job->payload();
            $jobData = unserialize($jobPayload['data']['command']);

            $chunk = ImportedComicChunk::where('chunk_number', $jobData->chunkIndex)->first();
            if ($event->job->hasFailed()) {
                $chunk->status = 'failed';
            } else {
                $chunk->status = 'processed';
            }
            $chunk->ended_at = Carbon::now()->toDateTimeString();
            $chunk->save();
        });
    }
}
