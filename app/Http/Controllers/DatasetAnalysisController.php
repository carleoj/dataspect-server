<?php

namespace App\Http\Controllers;

use App\Http\Resources\DatasetColumnsResource;
use App\Http\Resources\DatasetColumnTypesResource;
use App\Http\Resources\DatasetCorrelationResource;
use App\Http\Resources\DatasetDistributionsResource;
use App\Http\Resources\DatasetOverviewResource;
use Illuminate\Http\Request;

class DatasetAnalysisController extends Controller
{
    public function overview(Request $request, string $dataset)
    {
        return new DatasetOverviewResource($this->owned($request, $dataset));
    }

    public function columnTypes(Request $request, string $dataset)
    {
        return new DatasetColumnTypesResource($this->owned($request, $dataset));
    }

    public function distributions(Request $request, string $dataset)
    {
        return new DatasetDistributionsResource($this->owned($request, $dataset));
    }

    public function columns(Request $request, string $dataset)
    {
        return new DatasetColumnsResource($this->owned($request, $dataset));
    }

    public function correlation(Request $request, string $dataset)
    {
        return new DatasetCorrelationResource($this->owned($request, $dataset));
    }

    private function owned(Request $request, string $id)
    {
        return $request->user()->datasets()->findOrFail($id);
    }
}