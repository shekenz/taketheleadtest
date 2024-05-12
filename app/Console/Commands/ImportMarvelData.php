<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Carbon;
use Illuminate\Http\Client\Response;
use Exception;

class ImportMarvelData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'init:import-marvel-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Connect to Marvel API
        $timestamp = Carbon::now()->timestamp;
        $apikey = env('MARVEL_API_PUBLIC_KEY');
        $hash = md5($timestamp . env('MARVEL_API_PRIVATE_KEY') . $apikey);
        $authenticationParams = "?ts={$timestamp}&apikey={$apikey}&hash={$hash}";
        $marvelBaseUrl = "https://gateway.marvel.com/";

        $response = Http::get($marvelBaseUrl . 'v1/public/comics' . $authenticationParams . '&limit=' .  env('COMIC_CHUNK_SIZE') . '&orderBy=onsaleDate&format=comic');
        $responseData = json_decode($response->body());
        // $neededCalls = (int) $responseData->data->total / (int) $responseData->data->count;
        // $this->info("Needs {$neededCalls} API calls to get all results");
        if ($responseData->code > 299) {
            throw new Exception($responseData->status);
        }
        $this->info(json_encode($responseData));
    }
}
