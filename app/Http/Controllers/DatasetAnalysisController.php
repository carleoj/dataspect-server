<?php

namespace App\Http\Controllers;

use App\Http\Resources\DatasetOverviewResource;
use Illuminate\Http\Request;

class DatasetAnalysisController extends Controller
{
    public function overview(Request $request, string $dataset)
    {
        $dataset = $request->user()->datasets()->findOrFail($dataset);

        return new DatasetOverviewResource($dataset);
    }
}