<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DatasetColumnsResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return collect(data_get($this->profile, 'columns', []))
            ->map(fn ($c) => [
                'name'       => $c['name'],
                'type'       => $c['type'],
                'uniquePct'  => $c['unique_pct'],
                'missingPct' => $c['missing_pct'],
                'issues'     => $c['issues'],
                'stats'      => $c['stats'] ?? null, 
            ])
            ->values()
            ->all();
    }
}