<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DatasetDistributionsResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $histograms = data_get($this->profile, 'histograms', []);

        return [
            'columns'    => array_keys($histograms),
            'histograms' => (object) $histograms, 
        ];
    }
}