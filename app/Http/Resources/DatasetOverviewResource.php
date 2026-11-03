<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DatasetOverviewResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $overview = data_get($this->profile, 'overview', []);

        return [
            'status'           => $this->status,
            'rows'             => $this->row_count,
            'columns'          => $this->column_count,
            'missingCellsPct'  => $overview['missing_cells_pct'] ?? null,
            // per-dataset target, falling back to 2%
            'missingTargetPct' => data_get($this->options, 'missing_target_pct', 2),
            'duplicateRows'    => $overview['duplicate_rows'] ?? null,
            'duplicateRowsPct' => $overview['duplicate_rows_pct'] ?? null,
        ];
    }
}