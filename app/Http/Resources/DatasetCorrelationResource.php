<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DatasetCorrelationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'columns' => data_get($this->profile, 'correlation.columns', []),
            'values'  => data_get($this->profile, 'correlation.values', []),
        ];
    }
}