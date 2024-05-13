<?php

use Illuminate\Support\Facades\Route;
use App\Jobs\ProcessComicChunk;
use App\Models\Comic;
use Illuminate\Support\Facades\Log;
use App\Services\ComicAPIService;
use Illuminate\Bus\Batch;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Redis;
use App\Http\Resources\ComicCollection;
use App\Http\Resources\ComicResource;

Route::get('import-marvel-data', function () {

    $totalCount = ComicAPIService::fetchTotalCount();
    $chunkSize = (int) env('COMIC_CHUNK_SIZE', 20);
    $comicsLeft = $totalCount % $chunkSize;
    $jobsCount = floor($totalCount / $chunkSize) + 1;
    $jobsCount = env('MAX_CHUNK_IMPORT', $jobsCount);

    Log::channel('marvelapi')->info("Ready for launching {$jobsCount} jobs (Last chunk contains {$comicsLeft} comics)");

    $batchJobs = [];
    for ($i = 0; $i < $jobsCount; $i++) {
        $batchJobs[] = new ProcessComicChunk($i, ($i === $jobsCount - 1));
    }

    Bus::batch($batchJobs)->before(function () {
        Redis::set('comicsImportPending', true);
    })
    ->progress(function (Batch $batch) {
        Redis::set('comicsImportProgress', $batch->progress());
    })
    ->finally(function () {
        Log::channel('marvelapi')->info('Finished batch');
        Redis::set('comicsImportPending', false);
    })
    ->dispatch();

});

Route::get('import-status', function () {
    return response()->json([
        'importPending' => (bool) Redis::get('comicsImportPending'),
        'progress' => (int) Redis::get('comicsImportProgress'),
    ]);
});

// Route::apiResources('comics', Comic::class);

Route::get('comics', function() {
    return ComicResource::collection(Comic::paginate(10));
});
