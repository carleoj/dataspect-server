<?php

namespace App\Jobs;

use App\Models\Dataset;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class AnalyzeDataset implements ShouldQueue
{
    use Queueable, SerializesModels;

    public int $tries = 1;
    public int $timeout = 600;

    public function __construct(public Dataset $dataset) {}

    public function handle(): void
    {
        $this->dataset->update(['status' => 'processing']);

        $fileStream = Storage::disk('s3')->readStream($this->dataset->path);

        if (! $fileStream) {
            throw new \Exception("Could not read dataset file from S3: {$this->dataset->path}");
        }

        try {
            $result = Http::withHeaders(['X-API-Key' => config('services.analyzer.key')])
                ->timeout(540)
                ->attach('file', $fileStream, basename($this->dataset->path))
                ->post(rtrim(config('services.analyzer.url'), '/') . '/analyze', [
                    'delimiter' => (string) data_get($this->dataset->options, 'delimiter', ','),
                    'header'    => data_get($this->dataset->options, 'header', true) ? '1' : '0',
                ])
                ->throw()
                ->json();
        } finally {
            if (is_resource($fileStream)) {
                fclose($fileStream);
            }
        }

        $this->dataset->update([
            'row_count'    => $result['row_count'],
            'column_count' => $result['column_count'],
            'health_score' => $result['health_score'],
            'profile'      => $result['profile'],
            'status'       => 'ready',
        ]);
    }

    public function failed(\Throwable $e): void
    {
        $this->dataset->update([
            'status'  => 'failed',
            'options' => array_merge($this->dataset->options ?? [], [
                'error' => \Illuminate\Support\Str::limit($e->getMessage(), 500),
            ]),
        ]);
    }
}