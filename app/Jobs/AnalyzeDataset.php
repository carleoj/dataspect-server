<?php

namespace App\Jobs;

use App\Models\Dataset;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;

class AnalyzeDataset implements ShouldQueue
{
    use Queueable;

    public int $tries = 1;
    public int $timeout = 600;

    public function __construct(public Dataset $dataset) {}

    public function handle(): void
    {
        $this->dataset->update(['status' => 'processing']);

        $result = Http::withHeaders(['X-API-Key' => config('services.analyzer.key')])
            ->timeout(540)
            ->post(config('services.analyzer.url') . '/analyze', [
                'path'      => $this->dataset->path,
                'delimiter' => data_get($this->dataset->options, 'delimiter', ','),
                'header'    => data_get($this->dataset->options, 'header', true),
            ])
            ->throw()
            ->json();

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
        $this->dataset->update(['status' => 'failed']);
    }
}