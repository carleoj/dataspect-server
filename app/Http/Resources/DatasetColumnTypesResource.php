<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DatasetColumnTypesResource extends JsonResource
{
    private const COLORS = [
        'Numeric'     => '#4f46e5',
        'Categorical' => '#10b981',
        'Datetime'    => '#f59e0b',
        'Text'        => '#f43f5e',
    ];

    public function toArray(Request $request): array
    {
        return collect(data_get($this->profile, 'column_types', []))
            ->map(fn ($t) => [
                'label' => $t['label'],
                'count' => $t['count'],
                'color' => self::COLORS[$t['label']] ?? '#94a3b8',
            ])
            ->values()
            ->all();
    }
}