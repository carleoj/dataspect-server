<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Number;

class DatasetResource extends JsonResource
{
    private const HEALTHY_AT = 80; // score at or above this shows "Healthy"

    public function toArray(Request $request): array
    {
        return [
            'id'            => (string) $this->id,
            'name'          => $this->name,
            'rows'          => $this->row_count ?? 0,
            'columns'       => $this->column_count ?? 0,
            'sizeLabel'     => Number::fileSize($this->size_bytes, precision: 1),
            'uploadedLabel' => $this->uploadedLabel(),
            'status'        => $this->status,
            'health'        => match (true) {
                $this->health_score === null              => null,
                $this->health_score >= self::HEALTHY_AT   => 'healthy',
                default                                   => 'needs_attention',
            },
        ];
    }

    private function uploadedLabel(): string
    {
        $date = $this->created_at;

        return match (true) {
            $date->isToday()       => 'Today',
            $date->isYesterday()   => 'Yesterday',
            $date->isCurrentYear() => $date->format('M j'),
            default                => $date->format('M j, Y'),
        };
    }
}