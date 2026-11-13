<?php

namespace App\Jobs;

use App\Models\Dataset;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Queue\SerializesModels;

class AnalyzeDataset implements ShouldQueue
{
    use Queueable, SerializesModels;

    public int $tries = 1;
    public int $timeout = 600;

    public function __construct(public Dataset $dataset) {}

    public function handle(): void
    {
        $this->dataset->update(['status' => 'processing']);

        // 1. Get the file stream or contents directly from Neon Object Storage
        $fileStream = Storage::disk('s3')->readStream($this->dataset->path);

        if (!$fileStream) {
            throw new \Exception("Could not read dataset file from S3: {$this->dataset->path}");
        }

        // 2. Send the file securely via multipart/form-data to your analyzer service
        $response = Http::withHeaders(['X-API-Key' => config('services.analyzer.key')])
            ->timeout(540)
            ->attach(
                'file', // The form field name expected by your analyzer service
                $fileStream,
                basename($this->dataset->path)
            )
            ->post(config('services.analyzer.url') . '/analyze', [
                'delimiter' => data_get($this->dataset->options, 'delimiter', ','),
                'header'    => data_get($this->dataset->options, 'header', true),
            ]);

        if (is_resource($fileStream)) {
            fclose($fileStream);
        }

        $result = $response->throw()->json();

        // 3. Update the dataset with the analysis results
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