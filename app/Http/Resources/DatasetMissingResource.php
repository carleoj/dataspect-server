<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DatasetMissingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $m = data_get($this->profile, 'missing');

        return [
            'cells'           => $m['cells'] ?? null,
            'totalCells'      => ($this->row_count && $this->column_count) ? $this->row_count * $this->column_count : null,
            'columnsWithGaps' => $m['columns_with_gaps'] ?? null,
            'totalColumns'    => $this->column_count,
            'rowsWithGaps'    => $m['rows_with_gaps'] ?? null,
            'rowsWithGapsPct' => $m['rows_with_gaps_pct'] ?? null,
            'matrix'          => [
                'columns' => data_get($m, 'matrix.columns', []),
                'rows'    => data_get($m, 'matrix.rows', []),
            ],
        ];
    }
}