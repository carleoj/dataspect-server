<?php

namespace App\Http\Controllers;

use App\Http\Resources\DatasetColumnsResource;
use App\Http\Resources\DatasetColumnTypesResource;
use App\Http\Resources\DatasetCorrelationResource;
use App\Http\Resources\DatasetDistributionsResource;
use App\Http\Resources\DatasetOverviewResource;
use App\Http\Resources\DatasetHealthResource;
use App\Http\Resources\DatasetMissingResource;
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

    public function health(Request $request, string $dataset)
    {
        return new DatasetHealthResource($this->owned($request, $dataset));
    }

    public function missing(Request $request, string $dataset)
    {
        return new DatasetMissingResource($this->owned($request, $dataset));
    }
}
