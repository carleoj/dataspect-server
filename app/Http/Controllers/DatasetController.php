<?php

namespace App\Http\Controllers;

use App\Http\Requests\Dataset\StoreDatasetRequest;
use App\Http\Resources\DatasetResource;
use App\Jobs\AnalyzeDataset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Http\Requests\Dataset\RenameDatasetRequest;

class DatasetController extends Controller
{
    private const DELIMITERS = ['comma' => ',', 'semicolon' => ';', 'tab' => "\t", 'pipe' => '|'];

    public function index(Request $request)
    {
        return DatasetResource::collection($request->user()->datasets()->latest()->get());
    }

    public function store(StoreDatasetRequest $request)
    {
        $user = $request->user();
        $file = $request->file('file');
        $name = Str::limit($file->getClientOriginalName(), 250, '');
        $size = $file->getSize();

        $path = "datasets/{$user->id}/" . Str::uuid() . '.csv';

        // 1. Upload directly to Neon Object Storage (s3 disk)
        Storage::disk('s3')->putFileAs(
            "datasets/{$user->id}",
            $file,
            basename($path)
        );

        $dataset = $user->datasets()->create([
            'name'              => $name,
            'original_filename' => $name,
            'path'              => $path,
            'size_bytes'        => $size,
            'status'            => 'pending',
            'options'           => [
                'delimiter' => $this->resolveDelimiter(
                    $request->input('delimiter', 'auto'),
                    $path, // Pass the storage path instead of a local file path
                ),
                'header'    => $request->boolean('header', true),
            ],
        ]);

        AnalyzeDataset::dispatch($dataset);

        return (new DatasetResource($dataset))->response()->setStatusCode(201);
    }

    private function resolveDelimiter(string $choice, string $storagePath): string
    {
        if (isset(self::DELIMITERS[$choice])) {
            return self::DELIMITERS[$choice];
        }

        // Read the first chunk of the file safely from Neon Object Storage (S3)
        $stream = Storage::disk('s3')->readStream($storagePath);
        $line = '';
        
        if ($stream) {
            $line = fgets($stream, 65536) ?: '';
            fclose($stream);
        }

        $best = ',';
        $max = 0;
        foreach (self::DELIMITERS as $d) {
            $count = substr_count($line, $d);
            if ($count > $max) {
                $max = $count;
                $best = $d;
            }
        }

        return $best;
    }

    public function update(RenameDatasetRequest $request, string $dataset)
    {
        $dataset = $request->user()->datasets()->findOrFail($dataset);
        $dataset->update(['name' => $request->validated('name')]);

        return new DatasetResource($dataset);
    }

    public function destroy(Request $request, string $dataset)
    {
        $dataset = $request->user()->datasets()->findOrFail($dataset);

        // 2. Delete the file from Neon Object Storage instead of local storage
        Storage::disk('s3')->delete($dataset->path); 
        $dataset->delete();

        return response()->noContent();
    }

    public function reanalyze(Request $request, string $dataset)
    {
        $dataset = $request->user()->datasets()->findOrFail($dataset);

        if ($dataset->status === 'processing') {
            abort(409, 'This dataset is already being analyzed.');
        }

        $dataset->update(['status' => 'pending']);
        AnalyzeDataset::dispatch($dataset);

        return response()->noContent(202);
    }
}