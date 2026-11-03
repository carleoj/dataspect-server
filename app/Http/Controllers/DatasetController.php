<?php

namespace App\Http\Controllers;

use App\Http\Resources\DatasetResource;
use Illuminate\Http\Request;

class DatasetController extends Controller
{
    public function index(Request $request)
    {
        $datasets = $request->user()
            ->datasets()
            ->latest()
            ->get();

        return DatasetResource::collection($datasets);
    }
}