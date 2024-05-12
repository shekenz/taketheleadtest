<?php

namespace App\Services;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;

class ComicAPIService
{
    private const BASE_URL =  "https://gateway.marvel.com/v1/public/comics";

    public static function fetchTotalCount(): int
    {
        // Connect to Marvel API
        $params = [
            ...self::getAuthParams(),
            'limit' => 1,
            'format' => 'comic',
        ];
        $response = Http::get(self::BASE_URL . '?' . http_build_query($params));
        $responseData = json_decode($response->body());
        return $responseData->data->total;
    }

    public static function fetchChunk(int $chunkIndex): object
    {
        $chunkSize = (int) env('COMIC_CHUNK_SIZE', 20);
        $params = [
            ...self::getAuthParams(),
            'limit' => $chunkSize,
            'format' => 'comic',
            'orderBy' => 'onsaleDate',
            'offset' => $chunkSize * $chunkIndex,
        ];
        $response = Http::get(self::BASE_URL . '?' . http_build_query($params));
        return json_decode($response->body());
    }

    private static function getAuthParams(): array
    {
        $timestamp = Carbon::now()->timestamp;
        $apikey = env('MARVEL_API_PUBLIC_KEY');
        return [
            'ts' => $timestamp,
            'apikey' => $apikey,
            'hash' => md5($timestamp . env('MARVEL_API_PRIVATE_KEY') . $apikey),
        ];
    }
}
