<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DatasetMissingResource extends JsonResource
{
    private const MAX_MATRIX_COLUMNS = 20;

    public function toArray(Request $request): array
    {
        $m = data_get($this->profile, 'missing');

        [$columns, $rows] = $this->matrix(
            data_get($m, 'matrix.columns', []),
            data_get($m, 'matrix.rows', []),
        );

        return [
            'cells'           => $m['cells'] ?? null,
            'totalCells'      => ($this->row_count && $this->column_count) ? $this->row_count * $this->column_count : null,
            'columnsWithGaps' => $m['columns_with_gaps'] ?? null,
            'totalColumns'    => $this->column_count,
            'rowsWithGaps'    => $m['rows_with_gaps'] ?? null,
            'rowsWithGapsPct' => $m['rows_with_gaps_pct'] ?? null,
            'matrix'          => ['columns' => $columns, 'rows' => $rows],
        ];
    }

    /** @return array{0: array<int,string>, 1: array<int,array<int,bool>>} */
    private function matrix(array $columns, array $rows): array
    {
        if (count($columns) <= self::MAX_MATRIX_COLUMNS) {
            return [$columns, $rows];
        }

        $pct = collect(data_get($this->profile, 'columns', []))->pluck('missing_pct', 'name');

        $keep = collect(array_keys($columns))
            ->sortByDesc(fn ($i) => $pct->get($columns[$i], 0))
            ->take(self::MAX_MATRIX_COLUMNS)
            ->sort()
            ->values();

        return [
            $keep->map(fn ($i) => $columns[$i])->all(),
            array_map(fn ($row) => $keep->map(fn ($i) => $row[$i])->all(), $rows),
        ];
    }
}